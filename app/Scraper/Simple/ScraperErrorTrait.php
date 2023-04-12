<?php

namespace App\Scraper\Simple;

use App\Services\EmailNotificationService;
use App\Services\TelegramNotificationService;
use Illuminate\Support\Facades\Log;

trait ScraperErrorTrait
{
    private function sendScraperError(
        $data,
        $status,
        $url,
        $params,
        $limits
    ) {
        $alias = self::class;
        $date = now(config('app.timezone'))->format('Y-m-d\ H:i:sP');
        $host = request()->header('REFERER', config('app.url'));
        $requestParams = $params;
        $requestUrl = $url;
        $responseBody = $data;
        $responseCode = (string)$status;

        if (config('scrapers.notification.telegram.enabled')) {
            resolve(TelegramNotificationService::class)->sendScraperError(
                alias:  $alias,
                date:   $date,
                host:   $host,
                limits: $limits,
                requestParams: $requestParams,
                requestUrl:    $requestUrl,
                responseBody:  $responseBody,
                responseCode:  $responseCode,
            );
        }

        if (config('scrapers.notification.email.enabled')) {
            resolve(EmailNotificationService::class)->sendScraperError(
                alias:  $alias,
                date:   $date,
                host:   $host,
                limits: $limits,
                requestParams: $requestParams,
                requestUrl:    $requestUrl,
                responseBody:  $responseBody,
                responseCode:  $responseCode,
            );
        }
    }

    private function logScraperError(string $data): void
    {
        if (config('scrapers.log_enabled')) {
            Log::channel('scraper')->error($data);
        }
    }
}
