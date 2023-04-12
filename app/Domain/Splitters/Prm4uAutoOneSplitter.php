<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;
use App\Domain\Services\Prm4uAuto\APrm4uAuto;
use App\Exceptions\Reportable\ConfigurationException;

class Prm4uAutoOneSplitter extends ASplitter
{
    public function split(CompositeOrder $order, array $config): array
    {
        $distributionData = $this->distributionService->data($config)->all();
        self::throw(count($distributionData));

        $params = $order->params;
        $qty = $params['posts'] * avg($params['min'], $params['max']);

        $order->chunks()->create([
            'service_class' => APrm4uAuto::class,
            'details'       => [
                'charge' => 0.,
                'count'  => $qty,
                'link'   => 'https://instagram.com/' . $params['login'],
                'slot'   => $distributionData[0]['name'],
            ],
        ]);
        return ['Prm4uAutoOneChunk'];
    }

    static public function throw(int $count)
    {
        if ($count !== 1) {
            throw new ConfigurationException(__CLASS__ . ' needs exactly one slot');
        }
    }
}
