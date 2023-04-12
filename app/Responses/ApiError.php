<?php

namespace App\Responses;

class ApiError extends ApiResponse
{
    //{
    //"status":"error",
    //"message":"all bad!",
    //"data":[]
    //}

    public function __construct(string $message, $data=[])
    {
        if($data instanceof \Throwable && app()->environment() === 'local'){
            $data = static::prepareException($data);
        }
        parent::__construct(parent::STATUS_ERROR, $message, $data);
    }
}
