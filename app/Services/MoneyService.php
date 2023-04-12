<?php

namespace App\Services;

use App\Exceptions\NonReportable\BadCurrencyException;
use App\Exceptions\NonReportable\BadParameterException;
use App\Exceptions\NonReportable\InsufficientFundsException;
use App\PremiumStatus;
use App\Transaction;
use App\User;
use App\Withdraw;
use Illuminate\Support\Str;

/**
 * Class MoneyService
 * @package App\Services
 * @deprecated use \App\Services\Money\Services\TransactionService::class instead
 *
 * Сервис для всех финансовых операций
 */
class MoneyService
{
    public function getUserBalance($user, $cur): float
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }
        $balance = $user->transactions()
            ->where('cur', $cur)->get()->sum('amount');

        return round($balance, 2, PHP_ROUND_HALF_DOWN);
    }

    public function getPaymentsSum($user, $cur)
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        return $user->transactions()
            ->whereIn('type', [
                Transaction::INFLOW_PAYMENT,
                Transaction::INFLOW_CREATE
            ])
            ->where('cur', $cur)
            ->get()
            ->sum('amount');
    }

    public function inflow(
        $user,
        float $amount,
        string $cur,
        $type,
        $comment = '',
        $relatedId = null
    ) {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        if (!in_array($cur, Transaction::CUR)) {
            throw new BadCurrencyException($cur);
        }

        if ($amount < 0) {
            throw new BadParameterException(__('s.inflow_positive'));
        }

        $user->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'cur' => $cur,
            'comment' => $comment,
            'event_id' => null,
            'related_user_id' => $relatedId,
        ]);

        // premium status
        if (in_array($type, [Transaction::INFLOW_PAYMENT, Transaction::INFLOW_CREATE]) && $cur !== Transaction::CUR_UZS) {
            $spentInCurrency = $this->getPaymentsSum($user, $cur);
            $currentStatus = $user->premiumStatus;

            $premiumStatuses = PremiumStatus::where('cur', $cur)->get();

            foreach ($premiumStatuses as $ps) {
                if ($ps->cash > $currentStatus->cash &&
                    $ps->id > $currentStatus->id &&
                    $spentInCurrency >= $ps->cash) {
                    $currentStatus = $ps;
                }
            }

            $user->update(['premium_status_id' => $currentStatus->id]);
            $user->refresh();
        }
    }

    public function outflow($user, float $amount, string $cur, $type, $comment = '')
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        if (!in_array($cur, Transaction::CUR)) {
            throw new BadCurrencyException($cur);
        }

        if ($amount > 0) {
            throw new BadParameterException(__('s.outflow_negative'));
        }

        // $amount < 0
        if ($this->getUserBalance($user, $cur) - abs($amount) < 0) {
            throw new InsufficientFundsException();
        }

        return $user->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'cur' => $cur,
            'comment' => $comment,
            'event_id' => null,
            'related_user_id' => null,
        ]);
    }

    // снятие денег
    public function makeWithdraw($user, $withdrawType, $walletNum, $amount)
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        if ($amount > 0) {
            throw new BadParameterException(__('s.outflow_negative'));
        }

        $transaction = $user->transactions()->create([
            'type' => Transaction::OUTFLOW_WITHDRAWAL,
            'amount' => $amount,
            'comment' => null,
            'event_id' => null,
            'related_user_id' => null,
        ]);

        Withdraw::create([
            'transaction_id' => $transaction->id,
            'event_id' => 'todo_change_' . Str::random(18),
            'type' => $withdrawType,
            'wallet_number' => $walletNum,
        ]);
    }
}
