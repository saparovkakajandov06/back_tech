<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\Traits\Reportable;
use App\Exceptions\TException;

class ModeratorActionException extends TException
{
    use Reportable;

    protected $key = 'exceptions.bad_mod_action';
}
