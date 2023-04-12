<?php

namespace App\Domain\Validators;

use App\Scraper\Models\IgUser;
use App\ValidationResult;

class CheckLoginIsNotPrivate implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        if (IgUser::fromLogin($params['login'])->error) {
            return ValidationResult::invalid('login invalid or private');
        }

        return ValidationResult::valid('valid data');
    }
}
