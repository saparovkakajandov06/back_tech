<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;
use App\Domain\Models\Slots;
use App\Exceptions\Reportable\ConfigurationException;

class DefaultSplitter extends ASplitter
{
    public function split(CompositeOrder $order, array $config) : array
    {
        $distribution = $this
            ->distributionService
            ->data($config)
            ->distribution($order->params['count']);

        foreach ($distribution as $slotName => $count) {
            $slot = Slots::getSlotFromArray($slotName, $config)
                            ?? throw new ConfigurationException('Slot not found');

            if ($count > 0) {
                $order->chunks()->create([
                    'service_class' => $slot['service_class'],
                    'details'       => [
                        'slot'   => $slotName,
                        'link'   => $order->params['link'],
                        'count'  => $count,
                        'charge' => 0.,
                    ],
                ]);
            }
        }

        return $distribution;
    }
}
