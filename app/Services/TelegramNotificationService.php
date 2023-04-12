<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    private array $chats;
    private string $url;

    public function __construct()
    {
        $this->chats = config('scrapers.notification.telegram.chat_ids');
        $token = config('scrapers.notification.telegram.bot_token');
        $this->url = "https://api.telegram.org/bot$token/sendMessage";
    }

    private function send(string $text): void
    {
        foreach ($this->chats as $chat) {
            try {
                Http::timeout(5)
                    ->retry(2, 500)
                    ->post($this->url, [
                        'chat_id' => $chat,
                        'text'    => $text,
                        'disable_web_page_preview' => true
                    ])
                    ->throw();
            }
            catch (\Throwable $e) {
                Log::channel('scraper')
                    ->info("Scraper notification to telegram error: " . describe_exception($e));
            }
        }
    }

    public function sendScraperError(
        string $alias,
        string $date,
        string $host,
        string $limits,
        string $requestParams,
        string $requestUrl,
        string $responseBody,
        string $responseCode,
    ): void {
        $text = 'HOST: '   . $host   . PHP_EOL
            . 'Scraper: '  . $alias  . PHP_EOL
            . 'Endpoint: ' . $requestUrl    . PHP_EOL
            . 'Params: '   . $requestParams . PHP_EOL
            . 'Code: '     . $responseCode  . PHP_EOL
            . 'Response: ' . $responseBody  . PHP_EOL
            . 'Limits: '   . $limits . PHP_EOL
            . 'Date: '     . $date   . PHP_EOL;
        $this->send($text);
    }
}
