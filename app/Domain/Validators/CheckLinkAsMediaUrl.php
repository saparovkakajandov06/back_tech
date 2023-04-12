<?php

namespace App\Domain\Validators;

use App\Scraper\Models\IgMedia;
use App\ValidationResult;

class CheckLinkAsMediaUrl implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        if (app()->environment('testing')) {
            return ValidationResult::valid('testing');
        }

        $media = IgMedia::fromUrl($params['link']);
        if ($media->error) {
            return ValidationResult::invalid(
                'invalid instagram link: ' . $params['link']);
        }

        return ValidationResult::valid('valid data');
    }
}
