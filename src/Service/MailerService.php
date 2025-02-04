<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from('noreply@conge-facile.com')
            ->to($to)
            ->subject($subject)
            ->text($content)
            ->html("<p>{$content}</p>");

        $this->mailer->send($email);
    }
}
