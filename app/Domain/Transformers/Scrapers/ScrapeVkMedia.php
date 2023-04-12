<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Simple\ISimpleVkScraper;
use App\Scraper\Simple\VkApiScraper;
use App\UserService;

class ScrapeVkMedia implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        /** @var ISimpleVkScraper $scraper */
        $scraper = new $params['scraper'];
        $sdata = $scraper->media($params['link']);

        return array_merge($params, [
            'sdata' => $sdata,
        ]);
    }
}
