<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\Reportable;

class NameExistsException extends TException
{
    use Reportable;

    protected $key = 'exceptions.name_exists';
}
