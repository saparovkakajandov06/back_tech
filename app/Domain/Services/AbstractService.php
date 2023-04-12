<?php

namespace App\Domain\Services;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\Domain\Models\Slots;
use App\Exceptions\Reportable\NotImplementedException;
use App\Exceptions\Reportable\ReportableException;
use App\ExternStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

abstract class AbstractService
{
    const NET_COST_LOCAL = 'local';
    const NET_COST_REMOTE = 'remote';
    const NET_COST_AUTO = 'auto';
    const NET_COST_DISABLED = 'disabled';

    const TTL_HOURS = 2; // net cost cache

    /**
     * Добавить заказ во внешний сервис
     */
    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Получить статус заказа во внешнем сервисе по id
     */
    public function getStatus(int $orderId): ExternStatus
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Получить данные о себестоимости заказа во внешнем сервисе по id
     */
    public function charge(int $orderId, int $count, $svcConfig): float
    {
        throw new NotImplementedException(__METHOD__);
    }

    public static function getLocalCountWithMods($count, $config): int
    {
        $extra = $config['count_extra_percent'] ?? 0;
        $min = $config['count_min'] ?? 0;

        return max($count * (1 + $extra * 0.01), $min);
    }

    public static function getRemoteCountWithMods($count, $config): int
    {
        $local = self::getLocalCountWithMods($count, $config);

        $remote = $config['remote_extra_percent'] ?? 0;
        return $local * (1 + $remote * 0.01);
    }

    // slow version
    public function getManyStatuses(array $ids): array
    {
//        return collect($ids)->map(fn($id) => $this->getStatus($id))->all();
        echo "Using default mass update - slow\n";

        $statuses = [];

        foreach($ids as $id) {
            $statuses[] = $this->getStatus($id);
            echo $id . " ";
        }
//        return array_map(fn($id) => $this->getStatus($id), $ids);
        return $statuses;
    }

    public function netCost($externId, $count, $svcConfig): float
    {
        // $count - с локальными модификациями
        $amount = data_get($svcConfig, 'net_cost.amount', 100);
        $local = data_get($svcConfig, 'net_cost.local', 0);
        $mode = data_get($svcConfig, 'net_cost.mode', self::NET_COST_DISABLED);
        $auto = data_get($svcConfig, 'net_cost.auto', 0);
        $autoTimestamp = data_get($svcConfig, 'net_cost.auto_timestamp', null);

        if (empty($mode)) {
            throw new ReportableException('Net cost mode empty: ' . json_encode($svcConfig));
        }

	    Log::info('[CHUNK] ' . data_get($svcConfig, 'name', 'NONAME') . " in mode $mode");

        switch ($mode) {
            case self::NET_COST_LOCAL:
                return ($count / $amount) * $local;
            case self::NET_COST_REMOTE:
                return $this->charge($externId, $count, $svcConfig);
            case self::NET_COST_DISABLED:
                return 0.0;
            case self::NET_COST_AUTO:
                $makeRequest = false;
                if (empty ($autoTimestamp)) {
                    $makeRequest = true;
                }
                else {
                    $someHoursAgo = Carbon::now()->subHours(self::TTL_HOURS);
                    $ts = Carbon::parse($autoTimestamp);
                    if ($ts < $someHoursAgo) {
                        $makeRequest = true;
                    }
                }
                if ($makeRequest) {
                    $cost = $this->charge($externId, $count, $svcConfig);
                    $auto = ($cost / $count) * $amount; // rewrite $auto
                    Slots::mergeSlotNetCost($svcConfig['name'], [
                        'auto' => $auto,
                        'auto_timestamp' => Carbon::now()->toString(),
                    ]);
                }
                return ($count / $amount) * $auto;
            default:
                throw new ReportableException('Unknown net cost mode');
        }
    }
}
