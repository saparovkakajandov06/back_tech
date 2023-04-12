<?php

namespace App\Services\Search;

use App\Cashback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashbackSearchService extends BasicSearchService
{
    public function __construct()
    {
        parent::__construct();

        $this->query = Cashback::query()
                        ->select([
                            'id',
                            'user_id',
                            'type',
                            'amount',
                            'cur',
                            'comment',
                            'created_at',
                        ])
                        ->orderBy('id', 'desc');
    }

    public function setCur($cur): static
    {
        if (!empty($cur)) {
            $this->query->where('cur', Str::upper($cur));
        }
        return $this;
    }

    public function setTypes($types): static
    {
        if (!empty($types)) {
            $ts = Str::of($types)->trim()->upper()->explode(' ');
            $this->query->whereIn('type', $ts);
        }
        return $this;
    }

    // money totals by type
    public static function getMoneyTotalsByType($userId, $cur): object
    {
        $data = DB::table('cashback')
                ->where('user_id', $userId)
                ->where('cur', Str::upper($cur))
                ->groupBy('type')
                ->selectRaw('SUM(cashback.amount) AS amount, type')
                ->orderBy('type')
                ->get();

        return $data;
    }
}
