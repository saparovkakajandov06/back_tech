<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\VkApiScraper;
use App\UserService;

class SetVkApiScraper implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'scraper' => VkApiScraper::class,
        ]);
    }
}
