<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\TelegramScraper;
use App\UserService;

class SetTelegramScraper implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'scraper' => TelegramScraper::class,
        ]);
    }
}
