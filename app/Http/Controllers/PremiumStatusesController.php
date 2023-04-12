<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\PremiumStatus;
use App\User;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use Illuminate\Http\Request;
use App\Responses\ApiError;
use Illuminate\Support\Facades\Auth;

/**
 * @group premium_statuses
 *
 * Программа лояльности
 *
 */
class PremiumStatusesController extends Controller
{
    #[
        Group('other'),
        Endpoint('premium_statuses'),
        Verbs(D::GET),
        Text('Список статусов для программы лояльности')
    ]
    public function index(): ApiResponse
    {
        return new ApiSuccess('', PremiumStatus::all());
    }

    #[
        Group('other'),
        Endpoint('user/premium_statuses'),
        Role('ROLE_ANY'),
        Verbs(D::GET),
        Text('Список статусов для программы лояльности в валюте текущего пользователя')
    ]
    public function forUser(): ApiResponse
    {
        return new ApiSuccess(
            '',
            PremiumStatus::where('cur', Auth::user()->cur)
                ->get(['id', 'name', 'online_support', 'personal_manager', 'discount', 'cash'])
        );
    }

    #[
        Group('other'),
        Endpoint('admin/users/status'),
        Verbs(D::POST),
        Role('ROLE_MODERATOR'),
        Text('Изменение статуса юзера в программе лояльности'),
        Param('user_id', true, D::INT),
        Param('status_id', true, D::INT)
    ]
    public function update(Request $request): ApiResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'status_id' => 'required|integer|exists:premium_statuses,id',
        ]);

        $status = PremiumStatus::findOrFail($request->status_id);
        $user = User::findOrFail($request->user_id);

        if ($status->cur !== $user->cur) {
            return new ApiError('User and status must have the same currency');
        }

        $user->update([ 'premium_status_id' => $request->status_id ]);

        return new ApiSuccess('ok', $user);
    }
}
