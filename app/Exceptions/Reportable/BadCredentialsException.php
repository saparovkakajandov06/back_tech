<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\Reportable;

class BadCredentialsException extends TException
{
    use Reportable;

    protected $key = 'auth.failed';
}
