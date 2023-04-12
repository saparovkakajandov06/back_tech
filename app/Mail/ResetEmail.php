<?php

namespace App\Mail;

use App\Exceptions\NonReportable\BadLanguageException;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $reset_code;
    public $lang;
    public $host;

    const knownLanguages = ['ru', 'en', 'pt', 'tr', 'es', 'de', 'it', 'uk'];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reset_code, $host, $lang = 'ru')
    {
        if (! in_array($lang, self::knownLanguages)) {
            throw new BadLanguageException($lang);
        }

        $this->reset_code = $reset_code;
        $this->lang = $lang;
        $this->host = $host;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $templates = [
            'en' => 'emails.en.reset',
            'es' => 'emails.es.reset',
            'pt' => 'emails.pt.reset',
            'ru' => 'emails.ru.reset',
            'uk' => 'emails.uk.reset',
            'tr' => 'emails.tr.reset',
            'de' => 'emails.de.reset',
            'it' => 'emails.it.reset'
        ];

        $subjects = [
            'en' => 'Password recovery',
            'es' => 'Recuperación de contraseña',
            'pt' => 'Recuperação de senha',
            'ru' => 'Восстановление пароля',
            'uk' => 'Відновлення паролю',
            'tr' => 'şifre kurtarma',
            'de' => 'Passwort-Wiederherstellung',
            'it' => 'Recupero della password'
        ];

        return $this->view($templates[$this->lang])
                    ->subject($subjects[$this->lang]);
    }
}
