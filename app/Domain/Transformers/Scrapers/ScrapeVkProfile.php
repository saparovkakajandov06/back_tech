<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\ISimpleVkScraper;
use App\UserService;

class ScrapeVkProfile implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        /** @var ISimpleVkScraper $scraper */
        $scraper = new $params['scraper'];
        $sdata = $scraper->profile($params['login']);

        return array_merge($params, [
            'sdata' => $sdata,
        ]);
    }
}
