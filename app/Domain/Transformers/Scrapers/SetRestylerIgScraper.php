<?php

namespace App\Domain\Transformers\Scrapers;

use App\Domain\Transformers\ITransformer;
use App\UserService;

class SetRestylerIgScraper implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return $params;
    }
}
