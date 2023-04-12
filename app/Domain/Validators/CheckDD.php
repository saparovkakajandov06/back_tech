<?php

namespace App\Domain\Validators;

use App\ValidationResult;
use JetBrains\PhpStorm\NoReturn;

class CheckDD implements IValidator
{
    #[NoReturn]
    public function validate(array $params): ValidationResult
    {
        dd($params);
//        return ValidationResult::valid('ok', []);
    }
}
