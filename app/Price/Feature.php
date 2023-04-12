<?php

declare(strict_types=1);

namespace App\Price;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriceFeature
 *
 * @package App
 * @property string $name
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Feature extends Model
{
}

