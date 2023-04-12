<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\Reportable;

class EmailExistsException extends TException
{
    use Reportable;

    protected $key = 'exceptions.email_exists';
}
