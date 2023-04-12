<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status query()
 * @mixin \Eloquent
 */
class Status extends Model
{
    // bots
    const BOT_OK = 'BOT_OK';
    const BOT_ERROR = 'BOT_ERROR';
    const BOT_UNKNOWN = 'BOT_UNKNOWN';
}
