<?php

declare(strict_types=1);

namespace App\Notification;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'content'
    ];

    private const NOTIFICATION_TO_SHOW = 10;

    /**
     * @return Notification[]
     */
    public static function unread(): iterable
    {
        /** @var User $user */
        $user = Auth::user();

        /**
         * @var Status $lastStatus
         */
        $lastStatus = Status::getReadByUser($user);


        if(!$lastStatus) {
            $notifications = self::lasts();

            /** @var Notification $lastNotification */
            $lastNotification = $notifications->last();
            $lastNotification->getCreatedAtColumn();

            Status::new($lastNotification, $user);
        } else {
            $notifications = Notification::moreRecent($lastStatus->notification)->get();

            if($notifications->isNotEmpty()) {
                $lastStatus->changeNotification($notifications->last());
                $lastStatus->save();
            }
        }

        return $notifications;
    }

    public function scopeMoreRecent(Builder $query, Notification $notification)
    {
        return $query->where('id', '>', $notification->id)->take(self::NOTIFICATION_TO_SHOW);
    }

    public static function lasts(): Collection
    {
        return self::latest()->take(self::NOTIFICATION_TO_SHOW)->get();
    }
}
