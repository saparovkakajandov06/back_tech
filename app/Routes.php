<?php

namespace App;

use App\Role\UserRole;
use Illuminate\Support\Facades\Route;

class Routes
{
    public static function Auth(callable $routes)
    {
        Route::group(
            ['middleware' => 'auth:api'],
            fn() => $routes()
        );
    }

    public static function Admin(callable $routes)
    {
        Route::middleware([
            'auth:api',
            'check_user_role:'.UserRole::ROLE_ADMIN,
        ])
        ->group(fn() => $routes());
    }

    public static function Moderator(callable $routes)
    {
        Route::middleware([
            'auth:api',
            'check_user_role:'.UserRole::ROLE_MODERATOR,
        ])
            ->group(fn() => $routes());
    }

    public static function Manager(callable $routes)
    {
        Route::middleware([
            'auth:api',
            'check_user_role:'.UserRole::ROLE_MANAGER,
        ])
            ->group(fn() => $routes());
    }

    public static function Seo(callable $routes)
    {
        Route::middleware([
            'auth:api',
            'check_user_role:'.UserRole::ROLE_SEO,
        ])
            ->group(fn() => $routes());
    }

    public static function Proxy(callable $routes)
    {
        Route::middleware([
            'auth:api',
            'check_user_role:'.UserRole::ROLE_PROXY,
        ])
            ->group(fn() => $routes());
    }
}
