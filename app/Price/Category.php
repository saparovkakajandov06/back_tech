<?php

declare(strict_types=1);

namespace App\Price;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriceCategory
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{

}
