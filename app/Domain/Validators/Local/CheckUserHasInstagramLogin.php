<?php

namespace App\Domain\Validators\Local;

use App\Domain\Validators\IValidator;
use App\ValidationResult;
use Illuminate\Support\Facades\Auth;

class CheckUserHasInstagramLogin implements IValidator
{
    public function validate(array $params): ValidationResult
    {
        if(! empty((Auth::user()->instagram_login))) {
            return ValidationResult::valid('user has ig login');
        } else {
            $name = Auth::user()->name;
            return ValidationResult::invalid("user $name has no ig login");
        }
    }
}
