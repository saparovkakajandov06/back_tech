<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\KirtanTiktokScraper;
use App\UserService;

class SetKirtanTiktokScraper implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'scraper' => KirtanTiktokScraper::class,
        ]);
    }
}
