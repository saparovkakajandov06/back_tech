<?php

namespace App\Domain\Validators;

use App\ValidationResult;
use Illuminate\Support\Facades\Validator;

class CheckHasLoginCountPosts implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $validator = Validator::make($params, [
            'login' => 'required|string',
            'count' => 'required|integer',
            'posts' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ValidationResult::invalid('invalid data', $validator->errors());
        } else {
            return ValidationResult::valid('ok', []);
        }
    }
}
