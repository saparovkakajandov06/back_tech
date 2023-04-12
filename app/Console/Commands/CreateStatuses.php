<?php

namespace App\Console\Commands;

use App\PremiumStatus;
use App\Transaction;
use App\UserService;
use Illuminate\Console\Command;

class CreateStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'us:create_statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        PremiumStatus::truncate();

        // --------- RUB ------------

        PremiumStatus::create([
            'name' => 'Базовый',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 0,
                UserService::GROUP_VIEWS => 0,
                UserService::GROUP_SUBS => 0,
                UserService::GROUP_COMMENTS => 0,
                UserService::GROUP_OTHER => 0,
            ],

            'cash' => 0,
            'cur' => Transaction::CUR_RUB,
        ]);

        PremiumStatus::create([
            'name' => 'Персональный',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 5,
                UserService::GROUP_VIEWS => 7,
                UserService::GROUP_SUBS => 3,
                UserService::GROUP_COMMENTS => 5,
                UserService::GROUP_OTHER => 3,
            ],

            'cash' => 5000,
            'cur' => Transaction::CUR_RUB,
        ]);

        PremiumStatus::create([
            'name' => 'Премиум',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 10,
                UserService::GROUP_VIEWS => 15,
                UserService::GROUP_SUBS => 7,
                UserService::GROUP_COMMENTS => 10,
                UserService::GROUP_OTHER => 7,
            ],

            'cash' => 25000,
            'cur' => Transaction::CUR_RUB,
        ]);

        PremiumStatus::create([
            'name' => 'Блогер',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 15,
                UserService::GROUP_VIEWS => 25,
                UserService::GROUP_SUBS => 10,
                UserService::GROUP_COMMENTS => 15,
                UserService::GROUP_OTHER => 10,
            ],

            'cash' => 50000,
            'cur' => Transaction::CUR_RUB,
        ]);

        PremiumStatus::create([
            'name' => 'Элитный',

            'online_support' => 1,
            'personal_manager' => 1,

            'discount' => [
                UserService::GROUP_LIKES => 20,
                UserService::GROUP_VIEWS => 30,
                UserService::GROUP_SUBS => 15,
                UserService::GROUP_COMMENTS => 20,
                UserService::GROUP_OTHER => 15,
            ],

            'cash' => 100000,
            'cur' => Transaction::CUR_RUB,
        ]);

        // --------- USD ------------

        PremiumStatus::create([
            'name' => 'Базовый',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 0,
                UserService::GROUP_VIEWS => 0,
                UserService::GROUP_SUBS => 0,
                UserService::GROUP_COMMENTS => 0,
                UserService::GROUP_OTHER => 0,
            ],

            'cash' => 0,
            'cur' => Transaction::CUR_USD,
        ]);

        PremiumStatus::create([
            'name' => 'Персональный',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 5,
                UserService::GROUP_VIEWS => 7,
                UserService::GROUP_SUBS => 3,
                UserService::GROUP_COMMENTS => 5,
                UserService::GROUP_OTHER => 3,
            ],

            'cash' => 100,
            'cur' => Transaction::CUR_USD,
        ]);

        PremiumStatus::create([
            'name' => 'Премиум',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 10,
                UserService::GROUP_VIEWS => 15,
                UserService::GROUP_SUBS => 7,
                UserService::GROUP_COMMENTS => 10,
                UserService::GROUP_OTHER => 7,
            ],

            'cash' => 500,
            'cur' => Transaction::CUR_USD,
        ]);

        PremiumStatus::create([
            'name' => 'Блогер',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 15,
                UserService::GROUP_VIEWS => 25,
                UserService::GROUP_SUBS => 10,
                UserService::GROUP_COMMENTS => 15,
                UserService::GROUP_OTHER => 10,
            ],

            'cash' => 1000,
            'cur' => Transaction::CUR_USD,
        ]);

        PremiumStatus::create([
            'name' => 'Элитный',

            'online_support' => 1,
            'personal_manager' => 1,

            'discount' => [
                UserService::GROUP_LIKES => 20,
                UserService::GROUP_VIEWS => 30,
                UserService::GROUP_SUBS => 15,
                UserService::GROUP_COMMENTS => 20,
                UserService::GROUP_OTHER => 15,
            ],

            'cash' => 1500,
            'cur' => Transaction::CUR_USD,
        ]);

        return 0;
    }
}
