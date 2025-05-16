<?php

namespace App\Service;

use App\Entity\Request;
use App\Entity\User;
use App\Enum\Statut;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RequestService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerService $mailerService
    ) {}

    public function handleFileUpload(?UploadedFile $file, $person): ?string
    {
        if (!$file) return null;
        $firstName = strtolower($person->getFirstName());
        $lastName = strtolower($person->getLastName());
        $destination = $_SERVER['DOCUMENT_ROOT'] . "/files/{$firstName}_{$lastName}";
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }
        $uniqueId = uniqid();
        $date = (new \DateTime())->format('Y-m-d');
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $fileName = "{$firstName}_{$lastName}_{$date}_{$uniqueId}.{$extension}";
        $file->move($destination, $fileName);
        return $fileName;
    }

    public function createRequest(Request $theRequest, $person, $form): void
    {
        $theRequest->setCollaborator($person);
        $theRequest->setComment($form['comment']->getData() ?: null);
        $theRequest->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $theRequest->setAnswer(Statut::EnCours->value);
        $this->entityManager->persist($theRequest);
    }

    public function notifyManagerIfNeeded($person, $theRequest): void
    {
        $manager = $person->getManager();
        if ($manager && $manager->getAlertNewRequest()) {
            $managerUser = $this->entityManager->getRepository(User::class)->findOneBy(['person' => $manager]);
            if ($managerUser) {
                $this->mailerService->sendEmail(
                    $managerUser->getEmail(),
                    "CongéFacile : Nouvelle demande de congé déposée",
                    "{$person->getFirstName()} {$person->getLastName()} a déposé une demande de congé.<br>Merci de vous connecter à votre espace pour valider ou refuser la demande."
                );
            }
        }
    }

    public function notifyCollaboratorIfNeeded($request, $person, $result): void
    {
        $collaborator = $request->getCollaborator();
        $collaboratorUser = $this->entityManager->getRepository(User::class)->findOneBy(['person' => $collaborator]);
        $email = $collaboratorUser->getEmail();
        $alert = $collaborator->getAlertOnAnswer();

        if (true == $alert) {
            $to = $email;
            $subject = 'CongéFacile : Votre demande de congé à été ' . $result . 'e.';
            $message = '' . $person->getFirstName() . ' ' . $person->getLastName() . ' à ' . $result . ' votre demande de congé du ' . date_format($request->getCreatedAt(), 'd/m/Y') . '.';
            $this->mailerService->sendEmail($to, $subject, $message);
        }
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }
}