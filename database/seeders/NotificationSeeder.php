<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Notification\Notification;

class NotificationSeeder extends Seeder
{
    private $notifications = [
        'Обновление дня',
        'Сегодня празднуется день рождения',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->notifications as $content) {
            $notification = new Notification();
            $notification->content = $content;
            $notification->save();
        }
    }
}
