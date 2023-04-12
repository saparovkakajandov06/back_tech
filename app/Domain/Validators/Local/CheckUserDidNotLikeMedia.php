<?php

namespace App\Domain\Validators\Local;

use App\Domain\Validators\IValidator;
use App\Services\InstagramScraper\IInstagramScraper;
use App\ValidationResult;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CheckUserDidNotLikeMedia implements IValidator
{
    // todo fix scraper
    public function validate(array $params): ValidationResult
    {
        $scraper = App::make(IInstagramScraper::class);
        if ($scraper->didLike($params['link'], Auth::user()->instagram_login)) {
            return ValidationResult::invalid('already liked');
        } else {
            return ValidationResult::valid('ok');
        }
    }
}
