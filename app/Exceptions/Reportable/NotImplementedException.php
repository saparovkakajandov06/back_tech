<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class NotImplementedException extends TException
{
    use Reportable;

    protected $key = 'exceptions.not_implemented';
}
