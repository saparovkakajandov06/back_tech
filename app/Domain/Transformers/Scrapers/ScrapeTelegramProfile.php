<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\ISimpleTelegramScraper;
use App\UserService;

class ScrapeTelegramProfile implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        /** @var ISimpleTelegramScraper $scraper */
        $scraper = new $params['scraper'];
        $sdata = $scraper->profile($params['login']);

        return array_merge($params, [
            'sdata' => $sdata,
        ]);
    }
}
