<?php

namespace App\Domain\Validators;

use App\ValidationResult;
use Illuminate\Support\Facades\Validator;

class CheckHasLoginMinMaxPosts implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $validator = Validator::make($params, [
            'login' => 'required|string',
            'min' => 'required|integer',
            'max' => 'required|integer',
            'posts' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ValidationResult::invalid('invalid data', $validator->errors());
        } else {
            return ValidationResult::valid('ok', []);
        }
    }
}
