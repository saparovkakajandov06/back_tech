<?php

namespace App\Domain\Validators;

use App\ValidationResult;
use Illuminate\Support\Facades\Validator;

class CheckHasLinkAndCount implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $validator = Validator::make($params, [
            'link' => 'required|string', // ссылка на материал
            'count' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ValidationResult::invalid('invalid data', $validator->errors());
        } else {
            return ValidationResult::valid('ok', []);
        }
    }
}