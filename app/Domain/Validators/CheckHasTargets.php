<?php

namespace App\Domain\Validators;

use App\ValidationResult;
use Illuminate\Support\Facades\Validator;

class CheckHasTargets implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $validator = Validator::make($params, [
            'target1' => 'required|integer',
            'target2' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ValidationResult::invalid('invalid data', $validator->errors());
        } else {
            return ValidationResult::valid('ok', []);
        }
    }
}
