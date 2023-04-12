<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class ScraperException extends TException
{
    use Reportable;

    protected $key = 'exceptions.distribution_error';
}
