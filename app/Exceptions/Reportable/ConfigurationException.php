<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class ConfigurationException extends TException
{
    use Reportable;

    protected $key = 'exceptions.misconfiguration';
}
