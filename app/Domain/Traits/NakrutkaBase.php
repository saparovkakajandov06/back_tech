<?php

namespace App\Domain\Traits;

use Illuminate\Support\Facades\Http;

trait NakrutkaBase
{
    public $nakrutka;

    public function __construct()
    {
        $url = env('NAKRUTKA_API_URL');
        $key = env('NAKRUTKA_API_KEY');

        $this->nakrutka = new class($url, $key) {
            public function __construct($url, $key)
            {
                $this->url = $url;
                $this->key = $key;
            }

            // services, status, add
            public function __call($method, $arguments): array
            {
                $args = collect($arguments[0])
                    ->merge([
                        'key' => $this->key,
                        'action' => $method,
                    ])
                    ->all();

                return Http::get($this->url, $args)->json();
            }
        };
    }
}
