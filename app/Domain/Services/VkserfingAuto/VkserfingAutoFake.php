<?php

namespace App\Domain\Services\VkserfingAuto;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class VkserfingAutoFake extends AVkserfingAuto
{
    public const CHARGE = 111.11;

    protected $count;

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult
    {
        // total count
        $this->count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);
        $id = rand(0, 999999);

        return new AddResult(
            request: array_merge([
            'token' => 'hidden',
            'link' => $chunk->details['link'],
            'amount_users_limit' => $orderParams['count'], // per 1 post
            'amount_automatic_records_limit' => $orderParams['posts'], // posts
        ], $svcConfig['remote_params']), // type
            response: [
            'data' => [
                'id' => $id,
            ],
            'status' => 'success',
        ],
            externId: $id,
            status: Order::STATUS_RUNNING,
            charge: $this->netCost($id, $this->count, $svcConfig),
        );
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return self::CHARGE;
    }

    public function getStatus($orderId): ExternStatus
    {
        return new ExternStatus(
            status: Order::STATUS_RUNNING,
            remains: $this->count,
            response: [
            "data" => [
                "id" => "605284",
                "parent_id" => "0",
                "name" => "",
                "type" => "instagram_follower",
                "insurance" => "off",
                "link" => "https://www.instagram.com/chinesekitchenlbk/",
                "adult" => "off",
                "time" => "2020-03-03 15:44:06",
                "status" => "pause",
                "targeting" => [
                    "sex" => 0,
                    "relation" => "",
                    "age_from" => 0,
                    "age_to" => 0,
                    "friends_from" => 0,
                    "friends_to" => 0,
                    "subscribes_from" => 0,
                    "subscribes_to" => 0,
                    "profile_photos_from" => 0,
                    "profile_photos_to" => 0,
                    "records_from" => 0,
                    "records_to" => 0,
                    "month_reg_period" => 0,
                    "limit_per_day" => 960,
                    "automatic_records_limit" => "",
                    "automatic_old_records_limit" => 0,
                    "country" => null,
                    "city" => null,
                ],
                "money" => [
                    "spent" => 2.5,
                    "per_user" => 0.25,
                ],
                "users" => [
                    "limit_total" => 10,
                    "current" => 9,
                    "limit_system_per_hour" => 9,
                    "users_limit_random" => "off",
                    "automatic_current" => 0,
                    "automatic_records" => 0,
                    "left" => 1,
                ],
                "project" => null,
            ],
            "status" => "success",
        ]);
    }
}
