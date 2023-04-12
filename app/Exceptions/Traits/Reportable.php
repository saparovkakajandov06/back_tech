<?php

namespace App\Exceptions\Traits;

use Illuminate\Support\Facades\Log;

trait Reportable
{
    /**
     * Default implementation
     *
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        Log::error($this); // ok

        return null;
    }
}