<?php

namespace App\Responses;

class ApiSuccess extends ApiResponse
{
    //{
    //"status":"success",
    //"message":"all good!",
    //"data":[]
    //}

    public function __construct(string $message, $data=[])
    {
        parent::__construct(parent::STATUS_SUCCESS, $message, $data);
    }
}
