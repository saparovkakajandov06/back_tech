<?php

declare(strict_types=1);

namespace App\Notification;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Read
 *
 * @package App\Notification
 * @property Notification $notification
 * @property User $user
 * @property boolean $is_read
 * @property integer $user_id
 * @property integer $notification_id
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status readByUser(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status whereNotificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status whereUserId($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    public $timestamps = false;

    protected $table = 'notification_status';

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function changeNotification(Notification $notification): void
    {
        $this->notification_id = $notification->id;
    }

    public function changeUser(User $user): void
    {
        $this->user_id = $user->id;
    }

    public static function new(Notification $notification, User $user): self
    {
        $status = new self;

        $status->changeNotification($notification);
        $status->changeUser($user);
        $status->save();

        return $status;
    }

    public function scopeReadByUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', '=', $user->id)->latest();
    }

    public static function getReadByUser(User $user)
    {
        return self::where('user_id', '=', $user->id)->first();
    }
}

