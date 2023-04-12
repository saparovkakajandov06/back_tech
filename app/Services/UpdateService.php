<?php

namespace App\Services;

use App\Domain\Models\CompositeOrder as CO;
use App\Exceptions\Reportable\ReportableException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UpdateService
{
    const RANDOM = '15ecb9049ff33b_';
    const FLAG_RUNNING = self::RANDOM . 'FLAG_RUNNING';
    const FLAG_STOPPED = self::RANDOM . 'FLAG_STOPPED';
    const PROCESS_ODD = self::RANDOM . 'PROCESS_ODD';
    const PROCESS_EVEN = self::RANDOM . 'PROCESS_EVEN';
    const PROCESS_RECENT = self::RANDOM . 'PROCESS_RECENT';

    public function checkDate()
    {
        return Carbon::parse('07 may 2021');
    }

    public function restartDate()
    {
        return Carbon::parse('07 may 2021');
    }

    // --------- database -------

    public function getOrderIdsForUpdate(int $limit = 20): Collection
    {
        $idsLatest = CO::shouldBeUpdated()
            ->where('created_at', '>', Carbon::now()->subMinutes(5))
            ->pluck('id');

        if ($idsLatest->count() >= $limit) {
            return $idsLatest;
        }

        $idsOldest = CO::shouldBeUpdated()
            ->limit($limit - $idsLatest->count())
            //            ->inRandomOrder()
            ->orderBy('updated_at', 'asc')
            ->pluck('id');

        return $idsLatest->merge($idsOldest);
    }

    public function ordersForUpdate(string $type): Collection
    {
        $q = CO::shouldBeUpdated();
        $recentDate = Carbon::now()->subMinutes(60);

        match ($type) {
            'odd' => $q->where('created_at', '<', $recentDate)
                ->whereRaw('id % 2 = 1'),

            'even' => $q->where('created_at', '<', $recentDate)
                ->whereRaw('id % 2 = 0'),

            'recent' => $q->where('created_at', '>=', $recentDate),

            default => throw new ReportableException('bad update type'),
        };

        return $q->get();
    }

    // --------- cache -------

    public function keepRunning()
    {
        $ttl = env('AUTO_UPDATE_SLEEP', 10) + 10;
        Cache::put(self::FLAG_RUNNING, true, $ttl);
    }

    public function isRunning(): bool
    {
        return Cache::get(self::FLAG_RUNNING, false);
    }

    public function stop()
    {
        Cache::put(self::FLAG_STOPPED, true, 3600);
    }

    public function unstop()
    {
        Cache::forget(self::FLAG_STOPPED);
    }

    public function isStopped()
    {
        return Cache::get(self::FLAG_STOPPED, false);
    }

    public function sleep()
    {
        sleep(env('AUTO_UPDATE_SLEEP', 10));
    }

    // ----------------------------------
    public static function processKey($name): string
    {
        return self::RANDOM . 'PROCESS_' . Str::upper($name);
    }

    public function saveProcessPid(string $name, int $pid)
    {
        Cache::put(self::processKey($name), $pid, 3600);
    }

    public function deleteProcessPid(string $name)
    {
        Cache::forget(self::processKey($name));
    }

    public function getProcessPid(string $name): ?int
    {
        return Cache::get(self::processKey($name), null);
    }
}
