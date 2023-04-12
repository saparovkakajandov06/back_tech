<?php

namespace App\Services;

use App\Mail\ScraperErrorEmail;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
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
        Mail::to(config('scrapers.notification.email.to'))
            ->cc(config('scrapers.notification.email.cc'))
            ->queue(new ScraperErrorEmail(
                alias:  $alias,
                date:   $date,
                host:   $host,
                limits: $limits,
                requestParams: $requestParams,
                requestUrl:    $requestUrl,
                responseBody:  $responseBody,
                responseCode:  $responseCode,
            ));
    }
}
