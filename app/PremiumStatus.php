<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PremiumStatus
 *
 * @package App
 *
 * Статус пользователя для программы лояльности
 */
class PremiumStatus extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'online_support' => 'boolean',

        'discount' => 'array',

        'personal_manager' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
