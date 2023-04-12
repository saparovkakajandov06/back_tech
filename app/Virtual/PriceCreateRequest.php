<?php

declare(strict_types=1);

namespace App\Virtual;

/**
 * @OA\Schema(
 *     description="Creating request for a price",
 *     type="object",
 *     title="Creating request for a price"
 * )
 */
class PriceCreateRequest
{
    /**
     * @OA\Property(
     *     title="Cost",
     *     description="Cost for price",
     *     format="integer",
     *     example="10"
     * )
     */
    public $cost;

    /**
     * @OA\Property(
     *     title="Count for a price",
     *     description="Count for a price",
     *     format="integer",
     *     example="10"
     * )
     */
    public $count;

    /**
     * @OA\Property(
     *     title="Economy for a price",
     *     description="How much user will save on a buy",
     *     format="integer",
     *     example="20"
     * )
     */
    public $economy;

    /**
     * @OA\Property(
     *     title="Price Category Id",
     *     description="Give a category id of a price",
     *     format="integer",
     *     example="3"
     * )
     */
    public $category_id;

    /**
     * @OA\Property(
     *     title="Price feature",
     *     description="Decides whether price will be shown on a main page",
     *     format="boolean",
     *     example="true"
     * )
     */
    public $is_featured;

    /**
     * @OA\Property(
     *     title="Price's features",
     *     description="Provide all features, you wanna give for a price",
     *     format="array",
     *     example="[1, 10, 5]"
     * )
     */
    public $features;
}
