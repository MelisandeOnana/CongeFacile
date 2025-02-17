<?php

namespace App\Command;

use App\Repository\RequestRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[AsCommand(
    name: 'app:send-request-reminder',
    description: 'Envoie un email de rappel une semaine avant une date spécifique.',
)]
class SendRequestReminderCommand extends Command
{
    private $repository;
    private MailerService $mailerService;
    private EntityManagerInterface $entityManager;

    public function __construct(RequestRepository $repository, MailerService $mailerService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->mailerService = $mailerService;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new \DateTime('+7 days'); // Date dans une semaine
        $requests = $this->repository->findRequestsWithDate($date);
        $mails = 0;

        foreach ($requests as $request) {
            $answer = $request->getAnswer();
            if ($answer == 1) {
                $collaborator = $request->getCollaborator();
                $alert = $collaborator->getAlertBeforeVacation();
                if ($alert == true) {
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['person' => $collaborator]);
                    $to = $user->getEmail();
                    $subject = 'Votre congé commence dans une semaine !';
                    $message = 'Votre '.strtolower($request->getRequestType()->getName()).' débutant le '.$request->getStartAt()->format('d/m/Y').' et d\'une durée de '.$request->getWorkingdays().' jours arrive très bientôt.';
                    $this->mailerService->sendEmail($to, $subject, $message);
                    $mails++;
                }
            }
        }

        $output->writeln($mails.' emails de rappel envoyés avec succès.');
        return Command::SUCCESS;
    }
}
