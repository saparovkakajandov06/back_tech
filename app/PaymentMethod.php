<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Платежи через внешние системы
 * @property int $id
 * @property int $order
 * @property string|null $icon
 * @property array $titles
 * @property array $currencies
 * @property array $limits
 * @property array $countries
 * @property string $payment_system
 * @property string|null $gate_method_id
 * @property string $country_filter
 * @property boolean $show_agreement_flag
 * @property boolean $active_flag
 */
class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $casts = [
        'titles'              => 'array',
        'currencies'          => 'array',
        'limits'              => 'array',
        'countries'           => 'array',
        'show_agreement_flag' => 'boolean',
        'active_flag'         => 'boolean'
    ];

    protected $fillable = [
        'id',
        'order',
        'titles',
        'currencies',
        'limits',
        'countries',
        'payment_system',
        'gate_method_id',
        'country_filter',
        'show_agreement_flag',
        'active_flag'
    ];
}