<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class RequiredFieldException extends TException
{
    use Reportable;

    protected $key = 'exceptions.no_required_field';
}
