<?php

namespace App\Domain\Services\VkserfingAuto;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VkserfingAuto extends AVkserfingAuto
{
    const apiUrl = 'https://vkserfing.ru/api/campaign';
    const addUrl = self::apiUrl . '.add';
    const manyUrl = self::apiUrl . '.get';
    const statusUrl = self::apiUrl . '.getById';

    protected string $token;

    public function __construct()
    {
        $this->token = env('VKSERFING_TOKEN');
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        // amount_automatic_records_limit
        // Количество новых постов (лимит, определяющий сколько заданий будет создано)
        // пустое значение или параметр не передан - без ограничений
        // 0 - новые публикации сканироваться не будут
        $count = self::getLocalCountWithMods($orderParams['count'], $svcConfig);

        $request = array_merge([
            'amount_automatic_records_limit' => $orderParams['posts'], // posts
            'amount_users_limit'             => $count, // likes per 1 post
            'link'                           => $orderParams['link'],
            'order_id'                       => $chunk->compositeOrder->id,
            'status'                         => 'on',
            'token'                          => $this->token,
        ], $svcConfig['remote_params']); // type, comments_type
        $count *= $orderParams['posts'];

        // comments_type: custom, positive
        if (! empty($orderParams['comments'])) {
            $request['comments'] = array_map(
                fn($comment) => ['text' => $comment],
                $orderParams['comments']
            );
        }

        $response = null;
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get(self::addUrl, $request)
                ->throw();
            // Log::channel('suppliers')->info(__METHOD__ . $response->body());
            $response = $response->json();
        }
        catch (\Throwable $e) {
            Log::info(__METHOD__ . ": Bad response from provider");
            Log::info(describe_exception($e));
        }

        $request['token'] = 'hidden';
        $result = new AddResult(request: $request, response: $response);

        if ($response['status'] == 'success') {
            $result->status = Order::STATUS_RUNNING;
            $result->externId = Arr::get($response, 'data.id');
            $result->charge = $this->netCost($result->externId, $count, $svcConfig);
        }
        elseif ('error' === $response['status'] && $externalId = Arr::get($response, 'error.data.inner_id')) {
            $result->status = Order::STATUS_RUNNING;
            $result->externId = $externalId;
            $result->charge = $this->netCost($result->externId, $count, $svcConfig);
        }
        else {
            $result->status = Order::STATUS_ERROR;
        }
        return $result;
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get(self::statusUrl, [
                    'id'    => $orderId,
                    'token' => $this->token,
                ])
                ->throw();
            // Log::channel('suppliers')->info(__METHOD__ . $response->body());
            $response = $response->json();
            $perUser = Arr::get($response, 'data.money.per_user', 0.);
            return $perUser * $count;
        } catch (Exception $e) {
            Log::info('[CHUNK_CHARGE] ' . __CLASS__ . " could not get charge on order {$orderId}");
            Log::info('[CHUNK_CHARGE] ' . describe_exception($e));
        }
        return 0.;
    }

    private function statusFromData(?array $response): ExternStatus
    {
        $g = make_data_getter($response);

        $status = match ($g('status')) {
            'on', 'moderated', 'in_queue' => Order::STATUS_RUNNING,
            'pause' => $g('users.left') === 0
                ? Order::STATUS_COMPLETED
                : Order::STATUS_RUNNING,
            'finished' => Order::STATUS_COMPLETED,
            default => Order::STATUS_ERROR,
        };

        $es = new ExternStatus(
            status: $status,
            remains: $g('users.left'),
            response: $response,
        );

        $es->externId = $g('id');
        return $es;
    }

    public function getStatus($orderId): ExternStatus
    {
        $response = Http::retry(3, 1000)
            ->timeout(10)
            ->get(self::statusUrl, [
                'id'    => $orderId,
                'token' => $this->token,
            ])
            ->throw();
        // Log::channel('suppliers')->info(__METHOD__ . $response->body());
        $response = $response->json();

        echo "--------- vkserfing data ---------\n";
        echo json_encode($response);

        try {
            $externStatus = $this->statusFromData(Arr::get($response, 'data'));
        } catch (Exception $e) {
            echo "Vkserfing id {$orderId}" . PHP_EOL;
            echo describe_exception($e);
        }
        return $externStatus;
    }

    public function getManyStatuses(array $ids): array
    {
        $ids = collect($ids)->join(',');
        $response = Http::retry(3, 1000)
            ->timeout(10)
            ->get(self::manyUrl, [
                'id'    => $ids,
                'token' => $this->token,
            ])
            ->throw();
        // Log::channel('suppliers')->info(__METHOD__ . $response->body());
        $response = $response->json();

        $items = Arr::get($response, 'data.list');

        return collect($items)
            ->map(fn($item) => $this->statusFromData($item))
            ->all();
    }
}


