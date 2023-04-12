<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class DistributorException extends TException
{
    use Reportable;

    protected $key = 'exceptions.distributor_error';
}
