<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\Traits\NonReportable;
use App\Exceptions\TException;

class PipelineValidationException extends TException
{
    use NonReportable;

    protected $key = 'exceptions.pipeline_data_invalid';
}
