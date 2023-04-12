<?php

namespace App\Exceptions\Traits;

trait NonReportable
{
    /**
     * Return null
     *
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return null;
    }
}
