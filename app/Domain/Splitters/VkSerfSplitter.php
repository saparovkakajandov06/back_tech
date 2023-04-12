<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use App\Exceptions\Reportable\ConfigurationException;

class VkSerfSplitter extends ASplitter
{
    public function split(CompositeOrder $order, array $config): array
    {
        $distributionData = $this->distributionService->data($config)->all();
        self::throw(count($distributionData));

        $params = $order->params;
        $qty = $params['count'] * $params['posts'];

        $order->chunks()->create([
            'service_class' => AVkserfingAuto::class,
            'details'       => [
                'charge' => 0.,
                'count'  => $qty, // total likes in all posts
                'link'   => $params['link'],
                'slot'   => $distributionData[0]['name'],
            ],
        ]);

        return ['VkSerfSplitter'];
    }

    static public function throw(int $count)
    {
        if ($count !== 1) {
            throw new ConfigurationException(__CLASS__ . ' needs exactly one slot');
        }
    }
}
