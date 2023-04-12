<?php

namespace App\Documentor;

class Documentor
{
    const GET = 'GET';
    const POST = 'POST';
    const ANY = 'ANY';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    const VERBS = [
        self::GET,
        self::POST,
        self::ANY,
        self::PUT,
        self::DELETE
    ];

    const INT = 'int';
    const FLOAT = 'float';
    const STRING = 'string';
    const EMAIL = 'email';
    const IMAGE = 'image';
    const URL = 'url';
    const DATE = 'date';
    const BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';
    const OTHER = 'other';

    const TYPES = [
        self::INT,
        self::FLOAT,
        self::STRING,
        self::EMAIL,
        self::IMAGE,
        self::URL,
        self::DATE,
        self::BOOLEAN,
        self::TYPE_ARRAY,
        self::OTHER
    ];
}
