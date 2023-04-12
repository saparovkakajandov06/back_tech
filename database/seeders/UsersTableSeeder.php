<?php

namespace Database\Seeders;

use App\Role\UserRole;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'id' => 1000,
            'name' => 'admin',
            'email' => 'admin@smm.example.com',
            'password' => bcrypt('secret'),
            'api_token' => User::getFreeToken(),
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        User::create([
            'id' => 1001,
            'name' => 'moder',
            'password' => bcrypt('secret'),
            'email' => 'moder@smm.example.com',
            'roles' => [UserRole::ROLE_MODERATOR],
        ]);

        $user1 = User::create([
            'id' => 101,
            'name' => 'user1',
            'password' => bcrypt('secret'),
            'email' => 'user1@smm.example.com',
            'roles' => null,
        ]);

        $user2 = User::create([
            'id' => 102,
            'name' => 'user2',
            'password' => bcrypt('secret'),
            'email' => 'user2@smm.example.com',
            'roles' => null,
        ]);

        $user3 = User::create([
            'id' => 103,
            'name' => 'user3',
            'password' => bcrypt('secret'),
            'email' => 'user3@smm.example.com',
            'roles' => null,
        ]);

        $user4 = User::create([
            'id' => 104,
            'name' => 'user4',
            'password' => bcrypt('secret'),
            'email' => 'user4@smm.example.com',
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        $user5 = User::create([
            'id' => 1003,
            'api_token' => 'dnTGDla2LNnJYsSbPO4g8ZyM4avrB2aFmFoDqdWcfJwnuQs1aDtFBLW89Leg',
            'name' => 'Alan',
            'password' => '$2y$10$kvvdVwy20pQrqY4vCQdJ/.jptduicr5Wa2rdaIHWK3ulwt9bMoSqG',
            'email' => 'Jejs@ya.ru',
            'roles' => [UserRole::ROLE_ADMIN],
            'social_id' => '248467497',
            'network' => 'vkontakte',
            'avatar' => 'https://sun9-58.userapi.com/c624416/v624416497/24220/MQLQUTOq6p0.jpg?ava=1',
        ]);
    }
}
