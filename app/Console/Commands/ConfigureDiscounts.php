<?php

namespace App\Console\Commands;

use App\Domain\Models\Labels;
use App\PremiumStatus;
use Illuminate\Console\Command;
use US;

class ConfigureDiscounts extends Command
{
    protected $signature = 'configure:discounts';

    public $data = [
        US::VK_AUTO_LIKES_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_AUTO_LIKES
        ],
        US::VK_AUTO_LIKES_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_AUTO_LIKES
        ],
        US::VK_LIKES_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::VK_LIKES_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::VK_COMMENTS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_COMMENTS
        ],
        US::VK_COMMENTS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_COMMENTS
        ],
        US::VK_SUBS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_SUBS
        ],
        US::VK_SUBS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_SUBS
        ],


        US::INSTAGRAM_AUTO_VIEWS_MAIN => [
            Labels::DISCOUNT_VIEWS, Labels::DISCOUNT_AUTO_VIEWS
        ],
        US::INSTAGRAM_AUTO_LIKES_MAIN => [
            Labels::DISCOUNT_VIEWS, Labels::DISCOUNT_AUTO_LIKES
        ],
        US::INSTAGRAM_SUBS_MAIN => [
            Labels::DISCOUNT_VIEWS, Labels::DISCOUNT_SUBS
        ],
        US::INSTAGRAM_LIKES_MAIN => [
            Labels::DISCOUNT_VIEWS, Labels::DISCOUNT_LIKES
        ],
        US::INSTAGRAM_AUTO_LIKES_LK => [
            Labels::DISCOUNT_LIKES, Labels::DISCOUNT_AUTO_LIKES
        ],
        US::INSTAGRAM_AUTO_VIEWS_LK => [
            Labels::DISCOUNT_VIEWS, Labels::DISCOUNT_AUTO_VIEWS
        ],


        US::YOUTUBE_VIEWS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_VIEWS
        ],
        US::YOUTUBE_VIEWS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_VIEWS
        ],
        US::YOUTUBE_LIKES_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::YOUTUBE_DISLIKES_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::YOUTUBE_LIKES_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::YOUTUBE_SUBS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_SUBS
        ],
        US::YOUTUBE_SUBS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_SUBS
        ],
        US::YOUTUBE_DISLIKES_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],


        US::TIKTOK_SUBS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_SUBS
        ],
        US::TIKTOK_SUBS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_SUBS
        ],
        US::TIKTOK_AUTO_VIEWS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_AUTO_VIEWS
        ],
        US::TIKTOK_AUTO_LIKES_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_AUTO_LIKES
        ],
        US::TIKTOK_AUTO_LIKES_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_AUTO_LIKES
        ],
        US::TIKTOK_LIKES_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::TIKTOK_LIKES_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_LIKES
        ],
        US::TIKTOK_AUTO_VIEWS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_AUTO_VIEWS
        ],
        US::TIKTOK_VIEWS_LK => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_VIEWS
        ],
        US::TIKTOK_VIEWS_MAIN => [
            Labels::DISCOUNT_BASIC, Labels::DISCOUNT_VIEWS
        ]
    ];

    public $premiumStatusesData = [
        [
            'name' => 'Базовый',
            Labels::DISCOUNT_AUTO_LIKES => 0,
            Labels::DISCOUNT_AUTO_VIEWS => 0,
        ],
        [
            'name' => 'Персональный',
            Labels::DISCOUNT_AUTO_LIKES => 5,
            Labels::DISCOUNT_AUTO_VIEWS => 7,
        ],
        [
            'name' => 'Премиум',
            Labels::DISCOUNT_AUTO_LIKES => 10,
            Labels::DISCOUNT_AUTO_VIEWS => 15,
        ],
        [
            'name' => 'Блогер',
            Labels::DISCOUNT_AUTO_LIKES => 15,
            Labels::DISCOUNT_AUTO_VIEWS => 25,
        ],
        [
            'name' => 'Элитный',
            Labels::DISCOUNT_AUTO_LIKES => 20,
            Labels::DISCOUNT_AUTO_VIEWS => 30,
        ],
    ];

    public function updatePremiumStatuses()
    {
        foreach($this->premiumStatusesData as $data) {
            $name = $data['name'];

            $svc = PremiumStatus::where('name', $name)->get();

            echo "$name\n";

            foreach($svc as $ps) {
                $ds = $ps->discount;

                $ds[Labels::DISCOUNT_AUTO_LIKES] = $data[Labels::DISCOUNT_AUTO_LIKES];
                $ds[Labels::DISCOUNT_AUTO_VIEWS] = $data[Labels::DISCOUNT_AUTO_VIEWS];

                $ps->discount = $ds;
                $ps->save();

                echo json_encode($ps->discount);
                echo "\n";
            }
        }
    }

    public function updateDiscounts()
    {
        foreach($this->data as $tag => $labels) {
            [ $from, $to ] = $labels;

            $service = US::where('tag', $tag)->firstOrFail();

            echo "updating $tag from $from to $to\n";

            $service->replaceLabel($from, $to);
        }
    }

    public function handle()
    {
        $this->updatePremiumStatuses();

        $this->updateDiscounts();

        echo "\n--- done ---\n";
    }
}
