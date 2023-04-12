<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;
use App\Domain\Services\ATime;
use App\Exceptions\Reportable\ConfigurationException;

class TimeSplitter extends ASplitter
{
    public function split(CompositeOrder $order, array $config): array
    {
        $distributionData = $this->distributionService->data($config)->all();
        self::throw(count($distributionData));

        $link = $order->params['link'];
        $count = $order->params['count'];

        for ($i = 0; $i < $count; $i++) {
            $order->chunks()->create([
                'service_class' => ATime::class,
                'details'       => [
                    'link'  => $link . '_' . $i,
                    'count' => $count,
                    'slot'  => $distributionData[0]['name'],
                ],
            ]);
        }

        return ['time'];
    }

    static public function throw(int $count)
    {
        if ($count === 0) {
            throw new ConfigurationException(__CLASS__ . ' needs minimum one slot');
        }
    }
}
