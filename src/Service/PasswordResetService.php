<?php

namespace App\Service;

use App\Entity\User;
use App\Service\MailerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PasswordResetService
{
    public function __construct(
        private MailerService $mailerService,
        private ParameterBagInterface $params
    ) {}

    public function sendPasswordResetRequest(User $user, string $email): string
    {
        $to = $this->params->get('mailer_contact_email');
        $subject = sprintf(
            'CongéFacile : %s %s demande un changement de mot de passe.',
            $user->getPerson()->getFirstName(),
            $user->getPerson()->getLastName()
        );
        $message = sprintf(
            '%s %s demande un changement de mot de passe.<br>Adresse email de la personne : %s.<br><br>Après changement, merci de notifier l’utilisateur de son nouveau mot de passe.',
            $user->getPerson()->getFirstName(),
            $user->getPerson()->getLastName(),
            $email
        );

        try {
            $this->mailerService->sendEmail($to, $subject, $message);
            return 'Demande envoyée';
        } catch (\Exception $e) {
            return 'Demande non envoyée';
        }
    }
}