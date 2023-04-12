<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\USPrice
 *
 * @property int $id
 * @property string $tag
 * @property array|null $EUR
 * @property array|null $USD
 * @property array|null $RUB
 * @property array|null $TRY
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $BRL
 * @method static \Database\Factories\USPriceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereBRL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereEUR($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereRUB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereTRY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereUSD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class USPrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        Transaction::CUR_USD => 'array',
        Transaction::CUR_RUB => 'array',
        Transaction::CUR_EUR => 'array',
        Transaction::CUR_TRY => 'array',
        Transaction::CUR_BRL => 'array',
        Transaction::CUR_UAH => 'array',
        Transaction::CUR_UZS => 'array',
        'sale' => 'array',
    ];
}
