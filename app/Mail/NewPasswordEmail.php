<?php


namespace App\Mail;


use App\Exceptions\NonReportable\BadLanguageException;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $newPassword;
    public $lang;

    const knownLanguages = ['ru', 'en', 'pt', 'tr', 'es', 'de', 'it', 'uk'];

    public function __construct($newPassword, $lang = 'ru')
    {
        if (! in_array($lang, self::knownLanguages)) {
            throw new BadLanguageException($lang);
        }

        $this->newPassword = $newPassword;
        $this->lang = $lang;
    }
    public function build()
    {
        $templates = [
            'en' => 'emails.en.newpassword',
            'es' => 'emails.es.newpassword',
            'pt' => 'emails.pt.newpassword',
            'ru' => 'emails.ru.newpassword',
            'uk' => 'emails.uk.newpassword',
            'tr' => 'emails.tr.newpassword',
            'de' => 'emails.de.newpassword',
            'it' => 'emails.it.newpassword'
        ];

        $subjects = [
            'en' => 'New password',
            'es' => 'Nueva contraseña',
            'pt' => 'Nova senha',
            'ru' => 'Новый пароль',
            'uk' => 'Новий пароль',
            'tr' => 'Yeni şifre:',
            'de' => 'Neues Passwort ',
            'it' => 'Nuova password'
        ];

        return $this->view($templates[$this->lang])
            ->subject($subjects[$this->lang]);
    }

}