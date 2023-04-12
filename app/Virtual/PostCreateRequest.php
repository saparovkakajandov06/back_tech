<?php

declare(strict_types=1);

namespace App\Virtual;

/**
 * @OA\Schema(
 *     description="Creating request for a post",
 *     type="object",
 *     title="Creating request for a post"
 * )
 */
class PostCreateRequest
{
    /**
     * @OA\Property(
     *     title="Name",
     *     description="Post's name",
     *     format="string",
     *     example="We have a lunch today"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     title="Content",
     *     description="Content body",
     *     format="string",
     *     example="We have a lunch today"
     * )
     */
    public $content;
}
