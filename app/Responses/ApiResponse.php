<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

/** @noinspection ALL */

namespace App\Responses;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiResponse
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    protected string $status;
    protected ?string $message;
    protected $data;

    public function __construct(string $status, string $message, $data)
    {
        assert($status === self::STATUS_SUCCESS
            || $status === self::STATUS_ERROR);

        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    public function __toString(): string
    {
        return json_encode([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ], JSON_PRETTY_PRINT);
    }

//    public static function success(string $message, $data = null): ApiResponse
//    {
//        return new ApiResponse(self::STATUS_SUCCESS, $message, $data);
//    }
//
//    public static function error(string $message, $data = null): ApiResponse
//    {
//        return new ApiResponse(self::STATUS_ERROR, $message, $data);
//    }

    public function getData()
    {
        return $this->data;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public static function prepareException(\Throwable $e): array
    {
        return [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ];
    }
}
