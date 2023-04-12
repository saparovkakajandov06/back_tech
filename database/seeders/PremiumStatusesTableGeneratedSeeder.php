<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PremiumStatusesTableGeneratedSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        \DB::table('premium_statuses')->delete();
        
        \DB::table('premium_statuses')->insert(array (
            
            array (
                'id' => 1,
                'name' => 'LEVEL_1',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":0,"GROUP_VIEWS":0,"GROUP_SUBS":0}',
                'cash' => 0,
                'cur' => 'RUB',
            ),
            
            array (
                'id' => 2,
                'name' => 'LEVEL_2',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":5,"GROUP_VIEWS":7,"GROUP_SUBS":3}',
                'cash' => 5000,
                'cur' => 'RUB',
            ),
            
            array (
                'id' => 3,
                'name' => 'LEVEL_3',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":10,"GROUP_VIEWS":15,"GROUP_SUBS":7}',
                'cash' => 25000,
                'cur' => 'RUB',
            ),
            
            array (
                'id' => 4,
                'name' => 'LEVEL_4',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":15,"GROUP_VIEWS":25,"GROUP_SUBS":10',
                'cash' => 50000,
                'cur' => 'RUB',
            ),
            
            array (
                'id' => 5,
                'name' => 'LEVEL_5',
                'online_support' => true,
                'personal_manager' => true,
                'discount' => '{"GROUP_LIKES":20,"GROUP_VIEWS":30,"GROUP_SUBS":15}',
                'cash' => 100000,
                'cur' => 'RUB',
            ),
            
            array (
                'id' => 6,
                'name' => 'LEVEL_1',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":0,"GROUP_VIEWS":0,"GROUP_SUBS":0}',
                'cash' => 0,
                'cur' => 'USD',
            ),
            
            array (
                'id' => 7,
                'name' => 'LEVEL_2',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":5,"GROUP_VIEWS":7,"GROUP_SUBS":3}',
                'cash' => 100,
                'cur' => 'USD',
            ),
            
            array (
                'id' => 8,
                'name' => 'LEVEL_3',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":10,"GROUP_VIEWS":15,"GROUP_SUBS":7}',
                'cash' => 500,
                'cur' => 'USD',
            ),
            
            array (
                'id' => 9,
                'name' => 'LEVEL_4',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":15,"GROUP_VIEWS":25,"GROUP_SUBS":10}',
                'cash' => 1000,
                'cur' => 'USD',
            ),
            
            array (
                'id' => 10,
                'name' => 'LEVEL_5',
                'online_support' => true,
                'personal_manager' => true,
                'discount' => '{"GROUP_LIKES":20,"GROUP_VIEWS":30,"GROUP_SUBS":15}',
                'cash' => 1500,
                'cur' => 'USD',
            ),

            array (
                'id' => 11,
                'name' => 'LEVEL_1',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":0,"GROUP_VIEWS":0,"GROUP_SUBS":0}',
                'cash' => 0,
                'cur' => 'EUR',
            ),

            array (
                'id' => 12,
                'name' => 'LEVEL_2',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":5,"GROUP_VIEWS":7,"GROUP_SUBS":3}',
                'cash' => 100,
                'cur' => 'EUR',
            ),

            array (
                'id' => 13,
                'name' => 'LEVEL_3',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":10,"GROUP_VIEWS":15,"GROUP_SUBS":7}',
                'cash' => 500,
                'cur' => 'EUR',
            ),

            array (
                'id' => 14,
                'name' => 'LEVEL_4',
                'online_support' => true,
                'personal_manager' => false,
                'discount' => '{"GROUP_LIKES":15,"GROUP_VIEWS":25,"GROUP_SUBS":10}',
                'cash' => 1000,
                'cur' => 'EUR',
            ),

            array (
                'id' => 15,
                'name' => 'LEVEL_5',
                'online_support' => true,
                'personal_manager' => true,
                'discount' => '{"GROUP_LIKES":20,"GROUP_VIEWS":30,"GROUP_SUBS":15}',
                'cash' => 1500,
                'cur' => 'EUR',
            ),
        ));
    }
}