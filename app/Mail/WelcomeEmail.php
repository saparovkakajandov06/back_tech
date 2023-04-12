<?php

namespace App\Mail;

use App\Exceptions\NonReportable\BadLanguageException;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $login;
    public $email;
    public $password;
    public $lang;

    const knownLanguages = ['ru', 'en', 'pt', 'tr', 'es', 'de', 'it', 'uk'];

    public function __construct($login, $email, $password, $lang='ru')
    {
        if (! in_array($lang, self::knownLanguages)) {
            throw new BadLanguageException($lang);
        }

        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
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
            'en' => 'emails.en.welcome',
            'es' => 'emails.es.welcome',
            'pt' => 'emails.pt.welcome',
            'ru' => 'emails.ru.welcome',
            'uk' => 'emails.uk.welcome',
            'tr' => 'emails.tr.welcome',
            'de' => 'emails.de.welcome',
            'it' => 'emails.it.welcome'
        ];

        $subjects = [
            'en' => 'Welcome',
            'es' => '¡Bienvenidos!',
            'pt' => 'Bem-vinda!',
            'ru' => 'Добро пожаловать',
            'uk' => 'Ласкаво просимо',
            'tr' => 'Hoşgeldiniz!',
            'de' => 'Herzlich Willkommen!',
            'it' => 'Benvenuto!'
        ];

        return $this->view($templates[$this->lang])
                    ->subject($subjects[$this->lang]);
    }
}
