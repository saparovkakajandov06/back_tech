<?php

declare(strict_types=1);

/**
 * @OA\Schema(
 *     description="Creating request for a notification",
 *     type="object",
 *     title="Creating request for a notification"
 * )
 */
class NotificationPostRequest
{
    /**
     * @OA\Property(
     *     title="Content",
     *     description="Notification body",
     *     format="string",
     *     example="We have a lunch today"
     * )
     */
    public $content;
}
