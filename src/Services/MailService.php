<?php

namespace Abramenko\RestApi\Services;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class MailService extends Service
{
    public static function Send(string $email, string $body): void
    {
        // Тут настраиваем на свой почтовый сервер
        /*
        $transport = Transport::fromDsn('smtp://localhost');
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('a.abramenko@chita.ru')
            ->to($email)
            ->subject('Подтвертите Email')
            //->text('Sending emails is fun again!')
            ->html($body);

        $mailer->send($email);
        */
    }
}
