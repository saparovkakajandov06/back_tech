<?php

namespace App\Domain\Validators;

use App\ValidationResult;

interface IValidator
{
    public function validate(array $params): ValidationResult;
}
