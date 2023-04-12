<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\NonReportable;

class BadLanguageException extends TException
{
    use NonReportable;

    protected $key = 'exceptions.bad_language';
}
