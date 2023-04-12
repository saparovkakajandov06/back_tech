<?php

namespace App\Domain\Checkers;

use App\Action;
use App\ValidationResult;

interface ILocalChecker
{
    public function validate(Action $action): ValidationResult;
}
