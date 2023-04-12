<?php

namespace App\Domain\Validators;

use App\Scraper\Models\IgMedia;
use App\Scraper\PInstagramScraper;
use App\ValidationResult;

class RealCheckUserHasEnoughPosts implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $realScraper = new PInstagramScraper();

        $posts = IgMedia::fromLogin(
            $params['login'], $params['posts'], $realScraper);

        if (count($posts) < $params['posts']) {
            return ValidationResult::invalid('not enough posts', []);
        }

        return ValidationResult::valid('ok', []);
    }
}
