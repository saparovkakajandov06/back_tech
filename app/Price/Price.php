<?php

declare(strict_types=1);

namespace App\Price;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Price extends Model
{
    protected $fillable = [
        'cost',
        'count',
        'is_featured',
        'economy',
        'category_id'
    ];

    public static function create(array $properties): Price
    {
        $price = new Price();
        $price->fillOut($properties);

        return $price;
    }

    public function fillOut(array $properties): self
    {
        $this->fill($properties);
        $this->save();

        $this->features()->sync($properties['features']);

        return $this;
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'price_feature');
    }
}
