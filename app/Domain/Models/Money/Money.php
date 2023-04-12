<?php

namespace App\Domain\Models\Money;

use App\User;

interface Money
{
    public function applyTransaction(User|int $user);
}
