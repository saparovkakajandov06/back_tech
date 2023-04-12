<?php

namespace App\Console\Commands;

use App\Domain\Models\Labels;
use App\PremiumStatus;
use App\Transaction;
use Illuminate\Console\Command;

class AddEuroDiscounts extends Command
{
    protected $signature = 'configure:add_euro_discounts';

    public $premiumStatusesData = [[
        'name' => 'Базовый',
        'online_support' => 1,
        'personal_manager' => 0,
        'discount' => [
            Labels::DISCOUNT_BASIC => 0,
            Labels::DISCOUNT_LIKES => 0,
            Labels::DISCOUNT_VIEWS => 0,
            Labels::DISCOUNT_SUBS => 0,
            Labels::DISCOUNT_COMMENTS => 0,
            Labels::DISCOUNT_AUTO_LIKES => 0,
            Labels::DISCOUNT_AUTO_VIEWS => 0
        ],
        'cash' => 0,
        'cur'  => Transaction::CUR_EUR
    ], [
        'name' => 'Персональный',
        'online_support' => 1,
        'personal_manager' => 0,
        'discount' => [
            Labels::DISCOUNT_BASIC => 3,
            Labels::DISCOUNT_LIKES => 5,
            Labels::DISCOUNT_VIEWS => 7,
            Labels::DISCOUNT_SUBS => 3,
            Labels::DISCOUNT_COMMENTS => 5,
            Labels::DISCOUNT_AUTO_LIKES => 5,
            Labels::DISCOUNT_AUTO_VIEWS => 7
        ],
        'cash' => 100,
        'cur'  => Transaction::CUR_EUR
    ], [
        'name' => 'Премиум',
        'online_support' => 1,
        'personal_manager' => 0,
        'discount' => [
            Labels::DISCOUNT_BASIC => 7,
            Labels::DISCOUNT_LIKES => 10,
            Labels::DISCOUNT_VIEWS => 15,
            Labels::DISCOUNT_SUBS => 7,
            Labels::DISCOUNT_COMMENTS => 10,
            Labels::DISCOUNT_AUTO_LIKES => 10,
            Labels::DISCOUNT_AUTO_VIEWS => 15
        ],
        'cash' => 500,
        'cur'  => Transaction::CUR_EUR
    ], [
        'name' => 'Блогер',
        'online_support' => 1,
        'personal_manager' => 0,
        'discount' => [
            Labels::DISCOUNT_BASIC => 10,
            Labels::DISCOUNT_LIKES => 15,
            Labels::DISCOUNT_VIEWS => 25,
            Labels::DISCOUNT_SUBS => 10,
            Labels::DISCOUNT_COMMENTS => 15,
            Labels::DISCOUNT_AUTO_LIKES => 15,
            Labels::DISCOUNT_AUTO_VIEWS => 25
        ],
        'cash' => 1000,
        'cur'  => Transaction::CUR_EUR
    ], [
        'name' => 'Элитный',
        'online_support' => 1,
        'personal_manager' => 1,
        'discount' => [
            Labels::DISCOUNT_BASIC => 15,
            Labels::DISCOUNT_LIKES => 20,
            Labels::DISCOUNT_VIEWS => 30,
            Labels::DISCOUNT_SUBS => 15,
            Labels::DISCOUNT_COMMENTS => 20,
            Labels::DISCOUNT_AUTO_LIKES => 20,
            Labels::DISCOUNT_AUTO_VIEWS => 30
        ],
        'cash' => 1500,
        'cur'  => Transaction::CUR_EUR
    ]];

    public function updatePremiumStatuses()
    {
        foreach($this->premiumStatusesData as $data) {
            PremiumStatus::create($data);
        }
    }

    public function handle()
    {
        $this->updatePremiumStatuses();

        echo "\n--- done ---\n";
    }
}
