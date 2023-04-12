<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Exceptions\Reportable\ConfigurationException;
use App\Scraper\Models\IgMedia;

class NakrutkaMultiSplitter extends ASplitter
{
    public function split(CompositeOrder $order, array $config): array
    {
        $distributionData = $this->distributionService->data($config)->all();
        self::throw(count($distributionData));

        $posts = $order->params['posts'];
        $count = $order->params['count'];
        $login = $order->params['login'];
        $posts = IgMedia::fromLogin($login, $posts);
        $urls = array_map(fn ($p) => $p->link, $posts);

        foreach ($urls as $url) {
            $order->chunks()->create([
                'service_class' => ANakrutka::class,
                'details'       => [
                    'link'   => $url,
                    'count'  => $count,
                    'charge' => 0.,
                    'slot'   => $distributionData[0]['name'],
                ],
            ]);
        }

        return ['NakrutkaMultiSplitter'];
    }

    static public function throw(int $count)
    {
        if ($count === 0) {
            throw new ConfigurationException(__CLASS__ . ' needs minimum one slot');
        }
    }
}
