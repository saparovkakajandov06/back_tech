<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MakeNewAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_admins {users}';

    protected $description = 'Drop existing admin privileges, add new admins for each teammate (user arg format: login1:pass1,login2:pass2,...,loginN:passN)';

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
        $names = [
            'Dasha_moderator',
            'elena.igoshkina@list.ru',
            'fatimasmm',
            'ilnara',
            'moderator_alya',
            'moderator_ksenia',
            'moderator_maria',
            'moderator_mariia',
            'moderator_tanya',
            'moderator',
            'plus38269101287',
            'seo',
            'sh_botashev'
        ];
        User::whereNotIn('roles', ['["ROLE_AUTO"]', '', '[]'])
            ->whereNotIn('name', $names)
            ->update(['roles' => '']);
        $users = explode(',', $this->argument('users'));
        foreach ($users as $user) {
            [ $name, $password ] = explode(':', $user);
            $login = "admin_$name";
            $email = "$login@smmtouch.store";
            User::updateOrInsert([
                'email'            => Str::lower($email),
            ], [
                'api_token'        => User::getFreeToken(),
                'cur'              => 'RUB',
                'email'            => Str::lower($email),
                'lang'             => 'ru',
                'roles'            => '["ROLE_ADMIN"]',
                'name'             => Str::lower($login),
                'password'         => bcrypt($password),
                'token_updated_at' => Carbon::now(),
            ]);
            User::where('email', Str::lower($email))->first()->setBasicPremiumStatus();
            print("$login:$password -- ok" . PHP_EOL);
        }
        return 0;
    }
}
