<?php

namespace App\Scraper\Simple;

use Illuminate\Support\Facades\Http;

class TelegramScraper implements ISimpleTelegramScraper
{
    private string $key;
    private string $host;
    private string $serviceEndpoint;

    public function __construct()
    {
        $this->key = env('TELEGRAM_API_SCRAPER_BOT_TOKEN');
        $this->host = env('TELEGRAM_API_SCRAPER_HOST');
        $this->serviceEndpoint = env('TELEGRAM_VIEWS_SERVICE');
        
    }

    public function profile($login)
    {
        try {
            $res = Http::get("{$this->host}/bot{$this->key}/getChatMembersCount", [
                'chat_id' => '@' . $login
            ]);

            if($res->json('ok')){
                $parsed = [
                    'followers' => $res->json('result')
                ];
            } else {
                $parsed = $res->json();
            }

            return $parsed;
        } 
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }

    public function views($login, $id)
    {
        try {
            $res = Http::get($this->serviceEndpoint, [
                'login' => $login,
                'id' => $id
            ]);

            $parsed = [
                'views' => $res->json()
            ];
             
            return $parsed;
        } 
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }
}
