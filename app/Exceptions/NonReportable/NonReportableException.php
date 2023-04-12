<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\Traits\NonReportable;
use App\Exceptions\TException;

class NonReportableException extends TException
{
    use NonReportable;
}
