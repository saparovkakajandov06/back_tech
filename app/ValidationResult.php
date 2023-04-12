<?php

namespace App;

class ValidationResult
{
    public bool $isValid;
    public string $message;
    public $data;

    public function __construct($isValid, $message, $data)
    {
        $this->isValid = $isValid;
        $this->message = $message;
        $this->data = $data;
    }

    public static function valid($message, $data=null): ValidationResult
    {
        return new self(true, $message, $data);
    }

    public static function invalid($message, $data=null): ValidationResult
    {
        return new self(false, $message, $data);
    }
}
