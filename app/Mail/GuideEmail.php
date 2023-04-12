<?php

namespace App\Mail;

use App\Exceptions\NonReportable\BadLanguageException;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuideEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $lang;

    const knownLanguages = ['ru', 'en', 'pt', 'tr', 'es', 'de', 'it', 'uk'];

    public function __construct($email, $lang='ru')
    {
        if (! in_array($lang, self::knownLanguages)) {
            throw new BadLanguageException($lang);
        }

        $this->email = $email;
        $this->lang = $lang;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $templates = [
            'en' => 'emails.en.guide',
            'es' => 'emails.es.guide',
            'pt' => 'emails.pt.guide',
            'ru' => 'emails.ru.guide',
            'uk' => 'emails.uk.guide',
            'tr' => 'emails.tr.guide',
            'de' => 'emails.de.guide',
            'it' => 'emails.it.guide'
        ];

        $subjects = [
            'en' => 'Instagram promotion guide',
            'es' => 'Guía de promoción de Instagram',
            'pt' => 'Guia de promoção do Instagram',
            'ru' => 'Гайд по продвижению в Инстаграм',
            'uk' => 'Посібник з просування в Instagram',
            'tr' => 'Instagram tanıtım kılavuzu',
            'de' => 'Instagram-Promotion-Leitfaden',
            'it' => 'Guida alla promozione di Instagram'
        ];

        return $this->view($templates[$this->lang])
                ->subject($subjects[$this->lang])
                ->attach(storage_path("app/public/guides/instagram_guide_{$this->lang}.pdf"));
    }
}
