<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Responses\ApiSuccess;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    #[Endpoint('statistics')]
    #[Verbs(D::GET)]
    #[Group('order')]
    #[Text('Получение статистики по заказам')]
    public function all()
    {
        $monthAgo = Carbon::parse('1 month ago');
        $weekAgo = Carbon::parse('1 week ago');

        $all['ordersAllCount'] = DB::table('composite_orders')
            ->count();
        $all['ordersMonth'] = DB::table('composite_orders')
            ->where('created_at', '>=', $monthAgo)
            ->count();
        $all['ordersWeek'] = DB::table('composite_orders')
            ->where('created_at', '>=', $weekAgo)
            ->count();

        $all['localUsersCount'] = DB::table('users')
            ->where('roles', 'not like', "%ROLE_AUTO%")
            ->orWhereNull('roles')
            ->count();
        $all['localUsersMonth'] = DB::table('users')
            ->where('roles', 'not like', "%ROLE_AUTO%")
            ->orWhereNull('roles')
            ->where('created_at', '>=', $monthAgo)
            ->count();
        $all['localUsersWeek'] = DB::table('users')
            ->where('roles', 'not like', "%ROLE_AUTO%")
            ->orWhereNull('roles')
            ->where('created_at', '>=', $weekAgo)
            ->count();

        $all['ordersInWork'] = DB::table('composite_orders')
            ->where('status', 'STATUS_RUNNING')
            ->orWhere('status', 'STATUS_UPDATING')
            ->orWhere('status', 'STATUS_PAUSED')
            ->count();
        $all['ordersUpdating'] = DB::table('composite_orders')
            ->where('status', 'STATUS_UPDATING')
            ->count();
        $all['ordersError'] = DB::table('composite_orders')
            ->where('status', 'STATUS_ERROR')
            ->count();

        $all['services'] = DB::table('composite_orders')
            ->join('user_services', 'composite_orders.user_service_id', '=', 'user_services.id')
            ->select('composite_orders.user_service_id', 'user_services.tag', DB::raw('count(*) as total'))
            ->groupBy('user_service_id', 'user_services.tag')
            ->orderBy('total', 'DESC')
            ->get();

        return new ApiSuccess('success', $all);
    }
}
