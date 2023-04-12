<?php

namespace App\Domain\Interfaces;

interface ITracker
{
    public function getValue(array $params): ?int;
}
