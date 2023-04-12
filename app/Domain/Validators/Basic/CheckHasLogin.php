<?php

namespace App\Domain\Validators\Basic;

use App\Domain\Validators\IValidator;
use App\ValidationResult;

class CheckHasLogin implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        if (collect($params)->has('url')) {
            return ValidationResult::valid('ok');
        } else {
            return ValidationResult::invalid('no login');
        }
    }
}
