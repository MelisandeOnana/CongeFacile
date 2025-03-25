<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\RequestRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            if (1 == $answer) {
                $collaborator = $request->getCollaborator();
                $alert = $collaborator->getAlertBeforeVacation();
                if (true == $alert) {
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['person' => $collaborator]);
                    $to = $user->getEmail();

                    $subject = 'Votre congé commence dans une semaine !';

                    $requestType = strtolower($request->getRequestType()->getName());
                    $startAt = $request->getStartAt()->format('d/m/Y');
                    $workingDays = $request->getWorkingdays();
                    $dayLabel = $workingDays > 1 ? ' jours' : ' jour';

                    $message = "Votre $requestType débutant le $startAt et d'une durée de $workingDays$dayLabel arrive très bientôt.";

                    $this->mailerService->sendEmail($to, $subject, $message);
                    ++$mails;
                }
            }
        }

        $output->writeln($mails . ' email(s) de rappel envoyé(s).');

        return Command::SUCCESS;
    }
}
