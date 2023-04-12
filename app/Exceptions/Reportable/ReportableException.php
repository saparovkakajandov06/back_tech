<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class ReportableException extends TException
{
    use Reportable;
}
