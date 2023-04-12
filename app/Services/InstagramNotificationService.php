<?php


namespace App\Services;


use App\Order;
use App\Role\UserRole;
use Illuminate\Support\Facades\Http;

class InstagramNotificationService
{
    protected string $service_url;
    protected array $sendStatuses;

    public function __construct($config) {
        $this->service_url = $config['url'];
        $this->sendStatuses = $config['send'];
    }

    /*
    * ORDER->STATUS = STATUS_RUNNING
    * guest: [STATUS_COMPLETED]
    * ig_notification_statuses: [STATUS_COMPLETED]
    * */
    protected function checkOrderStatus($order) {
        $userType = in_array(UserRole::ROLE_AUTO, $order->user->roles ?: []) ? "guest" : "auth";

        if (!in_array($order->status, $this->sendStatuses[$userType])) {
            return false;
        }

        return true;
    }

    public function send($order) {
        if (!$this->checkOrderStatus($order)) {
            return null;
        }


        return Http::post($this->service_url . '/send', [
            "amount" => $order->params['cost'],
            "currency" => $order->params['cur'],
            "lang" => $order->params['locale'],
            "order_id" => $order->id,
            "quantity" => $order->params['count'],
            "status" => $order->status,
            "tag" => $order->userService->tag,
            "username" => '@' . $order->params['login']
        ]);
    }
}