<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScraperErrorEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public string $alias,
        public string $date,
        public string $host,
        public string $limits,
        public string $requestParams,
        public string $requestUrl,
        public string $responseBody,
        public string $responseCode
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.scraper_error')
            ->subject("Апи инстаграм: {$this->alias}, ошибка");
    }
}
