<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\ISimpleIgScraper;
use App\UserService;

class ScrapeIgMedia implements ITransformer
{
    private array $scrapers;

    public function __construct()
    {
        $this->scrapers = config('scrapers.instagram.list');
    }

    public function transform(array $params, UserService $us): array
    {
        if (!key_exists('code', $params)) {
            return $params;
        }

        foreach ($this->scrapers as $scraper) {
            try {
                $namespacedScraper = "\App\Scraper\Simple\\$scraper";

                /** @var ISimpleIgScraper $scraper */
                $scraper = new $namespacedScraper;

                $sdata = $scraper->media($params['code']);

                return array_merge($params, [
                    'login' => $sdata['login'],
                    'scraper' => $scraper::class,
                    'sdata' => $sdata,
                ]);
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $params;
    }
}
