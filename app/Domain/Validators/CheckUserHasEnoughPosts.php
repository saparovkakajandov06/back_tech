<?php

namespace App\Domain\Validators;

use App\Scraper\FakeInstagramScraper;
use App\Scraper\InstagramScraper;
use App\Scraper\Models\IgMedia;
use App\ValidationResult;

class CheckUserHasEnoughPosts implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $scraper = resolve(InstagramScraper::class);
        if (get_class($scraper) == FakeInstagramScraper::class) {
            return ValidationResult::valid('fake', []);
        }

        $posts = IgMedia::fromLogin($params['login'], $params['posts']);

        if (count($posts) < $params['posts']) {
            return ValidationResult::invalid('not enough posts', []);
        }

        return ValidationResult::valid('ok', []);
    }
}
