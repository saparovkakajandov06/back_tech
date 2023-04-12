<?php

namespace App\Repositories;

use App\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\User;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @deprecated
     */
    public function getGroup()
    {
        return DB::raw("
            CASE
                WHEN type IN (
                    '" . Transaction::INFLOW_REF_BONUS . "',
                    '" . Transaction::INFLOW_USER_JOB . "'
                ) THEN '" . Transaction::GROUP_EARNED . "'

                WHEN type IN (
                    '" . Transaction::OUTFLOW_WITHDRAWAL . "'
                ) THEN '" . Transaction::GROUP_WITHDRAWN . "'

                WHEN type IN (
                    '" . Transaction::INFLOW_TEST . "',
                    '" . Transaction::INFLOW_OTHER . "',
                    '" . Transaction::INFLOW_CREATE . "',
                    '" . Transaction::INFLOW_REFUND . "',
                    '" . Transaction::INFLOW_PAYMENT . "'
                ) THEN '" . Transaction::GROUP_DEPOSITED . "'

                ELSE '" . Transaction::GROUP_UNKNOWN . "'
            END AS transaction_group
        ");
    }

    /**
     * @deprecated
     */
    public function getGroupByUser(
        int $userId,
        string $dateFrom = null,
        string $dateTo = null,
        string $transactionGroup = null,
        string $cur = null,
        int $offset,
        int $limit,
    ): array {
        $q = Transaction::query();

        $q->where('type', '!=', Transaction::OUTFLOW_ORDER)
            ->where('user_id', $userId)
            ->select([
                'id',
                'amount',
                'commission',
                'comment',
                'created_at',
                self::getGroup(),
                'cur'
            ]);

        if (!empty($cur)) {
            $q->where('cur', $cur);
        }

        if (!empty($dateFrom)) {
            $q->where('created_at', '>=', Carbon::parse($dateFrom));
        }

        if (!empty($dateTo)) {
            $q->where('created_at', '<=', Carbon::parse($dateTo));
        }

        if (!empty($transactionGroup)) {
            $q->whereIn('type', Transaction::TRANSACTION_GROUP[$transactionGroup]);
        }

        $count = $q->count();

        $q->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($limit);

        return [$count, $q->get()];
    }

    /** @deprecated */
    // money totals by group
    public function getTotals(int $userId, string $cur = null): object
    {
        $slash = $cur ? '_' : '';

        $data = DB::table('transaction_groups as tg')
            ->leftJoin('transaction_types as tt', 'tt.transaction_group_id', '=', 'tg.id')
            ->leftJoin('transactions as t', function ($join) use ($userId, $cur) {
                $join->on('t.type', '=', 'tt.transaction_type')
                    ->where('t.user_id', $userId)
                    ->whereIn('t.cur', $cur ? [$cur] : Transaction::CUR);
            })
            ->whereIn('tg.transaction_group', [
                Transaction::GROUP_WITHDRAWN,
                Transaction::GROUP_EARNED,
                Transaction::GROUP_DEPOSITED
            ])
            ->groupBy('tg.id')
            ->selectRaw('COALESCE(SUM(t.amount), 0) AS amount' . $slash . $cur . ', tg.transaction_group as group, tg.title')
            ->get();

        return $data;
    }

    public function getRefBonuses(
        int $parentId,
        int $transactionsOffset = 0,
        int $transactionsLimit = 10,
    ): object {
        $data = DB::table('users as u')
            ->leftJoin('transactions as t', 't.related_user_id', '=', 'u.id')
            ->where('u.parent_id', $parentId)
            ->where(function ($query) {
                $query->whereNull('t.type')
                    ->orWhere('t.type', '=', Transaction::INFLOW_REF_BONUS);
            })
            ->groupBy('u.name', 'u.avatar', 'u.created_at', 'u.cur')
            ->selectRaw('COALESCE(SUM(t.amount), 0) AS sum, u.name, u.avatar, u.created_at AS date')
            ->orderBy('sum', 'desc')
            ->orderBy('u.created_at', 'desc')
            ->skip($transactionsOffset)
            ->take($transactionsLimit)
            ->get();

        return $data;
    }

    public function getRefBonusesTotal(int $parentId)
    {
        $data = DB::table('users as u')
            ->leftJoin('transactions as t', 't.related_user_id', '=', 'u.id')
            ->where('u.parent_id', $parentId)
            ->where(function ($query) {
                $query->whereNull('t.type')
                    ->orWhere('t.type', '=', Transaction::INFLOW_REF_BONUS);
            })
            ->selectRaw('COALESCE(SUM(t.amount), 0) AS sum')
            ->get();

        return $data->first()->sum;
    }
}
