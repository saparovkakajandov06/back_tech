<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;

interface ISplitter
{
    public function split(CompositeOrder $order, array $config): array;

    static public function throw(int $count);
}
