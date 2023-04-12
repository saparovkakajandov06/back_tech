<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Курсы валют
 * @property int $id
 * @property string $sid
 * @property float $rate
 */
class Currency extends Model
{
    use HasFactory;

    public $timestamps = true;
}
