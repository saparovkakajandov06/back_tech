<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;

class NullSplitter implements ISplitter
{
    public function split(CompositeOrder $order, array $config): array
    {
        return ['null'];
    }
}
