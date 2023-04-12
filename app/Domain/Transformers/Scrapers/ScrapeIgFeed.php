<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ScraperException;
use App\Scraper\Simple\ISimpleIgScraper;
use App\UserService;

class ScrapeIgFeed implements ITransformer
{
    private array $scrapers;

    public function __construct()
    {
        $this->scrapers = config('scrapers.instagram.list');
    }

    public function transform(array $params, UserService $us): array
    {
        if ($params['posts'] < 1 || $params['posts'] > 100) {
            throw new ScraperException('Post count must be in range [ 1, 100 ], ' . $params['posts'] . ' given.');
        }

        $data = [];
        foreach ($this->scrapers as $scraper) {
            try {
                $namespacedScraper = "\App\Scraper\Simple\\$scraper";
                /** @var ISimpleIgScraper $scraper */
                $scraper = new $namespacedScraper;
                
                $data = [
                    'sdata' => $scraper->feed($params['login'], $params['posts']),
                    'scraper' => $scraper::class
                ];

                break;
            } catch (\Throwable $e) {
                continue;
            }
        }

        return array_merge($params, $data);
    }
}
