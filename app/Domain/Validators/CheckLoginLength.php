<?php

namespace App\Domain\Validators;

use App\ValidationResult;

class CheckLoginLength implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $length = strlen($params['login']);

        echo "length = $length\n";

        if ($length > 6) {
            return ValidationResult::invalid('invalid login length', []);
        } else {
            return ValidationResult::valid('ok', []);
        }
    }
}
