<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\ISimpleTiktokScraper;
use App\UserService;

class ScrapeTiktokProfile implements ITransformer
{
    private array $scrapers;

    public function __construct()
    {
        $this->scrapers = config('scrapers.tiktok.list');
    }

    public function transform(array $params, UserService $us): array
    {
        if (!key_exists('login', $params) || !$params['login']) {
            return $params;
        }

        foreach ($this->scrapers as $scraper) {
            try {
                $namespacedScraper = "\App\Scraper\Simple\\$scraper";
                
                /** @var ISimpleTiktokScraper $scraper */
                $scraper = new $namespacedScraper;

                $sdata = $scraper->profile($params['login']);

                return array_merge($params, [
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
