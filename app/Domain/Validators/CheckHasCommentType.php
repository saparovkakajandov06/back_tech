<?php

namespace App\Domain\Validators;

use App\ValidationResult;
use Illuminate\Support\Facades\Validator;

class CheckHasCommentType implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        $validator = Validator::make($params, [
//            'comment_type' => [
//                'required',
//                Rule::in(['positive', 'own', 'custom']),
//            ],
//            'comments' => [
//                    'exclude_if:comment_type, "positive"',
//                    'required',
//            ],
        ]);

        if ($validator->fails()) {
            return ValidationResult::invalid('invalid data',
                $validator->errors());
        } else {
            return ValidationResult::valid('ok', []);
        }
    }
}