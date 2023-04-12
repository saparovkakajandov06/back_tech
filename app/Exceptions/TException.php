<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class TException extends Exception
{
    protected $key = 'exceptions.cat';
    protected $data = null;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = __($this->key);
        }

        parent::__construct($message, $code, $previous);
    }

    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function __toString(): string
    {
        $description = [
            'status' => 'error',

            'error' => get_class($this),
            'message' => $this->getMessage(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'request' => request()?->all(),
        ];

        if ($this->data) {
            $description['data'] = $this->data;
        }

        return json_encode($description, JSON_PRETTY_PRINT);
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        Log::error($this);

        return null;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | array
     */
    public function render($request)
    {
        return response(
            $this->__toString(),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
