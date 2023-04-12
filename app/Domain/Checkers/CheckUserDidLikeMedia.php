<?php

namespace App\Domain\Checkers;

use App\Action;
use App\Services\InstagramScraper\IInstagramScraper;
use App\ValidationResult;
use Illuminate\Support\Facades\App;

class CheckUserDidLikeMedia implements ILocalChecker
{
    // todo fix scraper
    public function validate(Action $action): ValidationResult
    {
        $link = $action->chunk->compositeOrder->params['link'];
        $ig_login = $action->user->instagram_login;

        $scraper = App::make(IInstagramScraper::class);

        if ($scraper->didLike($link, $ig_login)) {
            return ValidationResult::valid('ok');
        } else {
            return ValidationResult::invalid('like not found');
        }
    }
}
