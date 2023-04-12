<?php

namespace App\Domain\Validators;

use App\Services\Money\Services\TransactionsService;
use App\ValidationResult;
use Illuminate\Support\Facades\Auth;

class CheckUserHasEnoughFunds implements IValidator
{
    public function validate(array $input): ValidationResult
    {
        $total = 0;

        foreach($input as $params) {
            if(isset($params['app']) && $params['app'])
                return ValidationResult::valid('valid data');
            $total += $params['cost'];
        }

        $balance = resolve(TransactionsService::class)->sum(Auth::user(), Auth::user()->cur);
        $diff = $balance - $total;

        if ($diff < 0) {
            return ValidationResult::invalid("Not enough funds: $diff " . Auth::user()->cur);
        }
        return ValidationResult::valid('valid data');
    }
}
