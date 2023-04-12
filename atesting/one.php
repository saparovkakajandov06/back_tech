<?php

require_once '../vendor/autoload.php';
require_once './models/ATUser.php';
use App\Atesting\Models\ATUser;
use Illuminate\Support\Str;


$email = 'abc' . random_int(1, 2000) . '@mailto.plus';
$login = 'test_' . Str::lower(Str::random(12));
$pass = 'sdljlkgj';

$user = ATUser::register($email, $login, $pass);
if (! empty($user)) {
    echo "*** registered user" . PHP_EOL;
    $user->loadDetails();
    print_r($user);
}

//$status = data_get(json_decode($body), 'status');
//
//echo $status, "\n";
//
//echo "done\n";
