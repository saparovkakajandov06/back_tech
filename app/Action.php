<?php

namespace App;

use App\Domain\Models\Chunk;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $guarded = [];

    protected $casts = [
        'completed' => 'boolean',
        'paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chunk()
    {
        return $this->belongsTo(Chunk::class);
    }
}
