<?php

namespace App\Controller;

use App\Entity\Request;
use App\Entity\User;
use App\Enum\Statut;
use App\Form\AnswerType;
use App\Form\RequestForm;
use App\Form\HistoricRequestSearchType;
use App\Form\PendingRequestSearchType;
use App\Repository\PersonRepository;
use App\Repository\RequestRepository;
use App\Repository\RequestTypeRepository;
use App\Service\MailerService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RequestController extends AbstractController
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    #[Route('/request/historic', name: 'request_historic', methods: ['GET'])]
    public function request_historic(HttpRequest $request, RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository, PaginatorInterface $paginator, PersonRepository $personRepository): Response
    {
        $user = $this->getUser();

        if (! $user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }

        $person = $user->getPerson();
        $requestTypes = $requestTypeRepository->findAll();

        if ('ROLE_COLLABORATOR' == $user->getRole()) {
            $collaborators = [];
        } else {
            $collaborators = $personRepository->getPersonByManager($person);
        }

        $form = $this->createForm(HistoricRequestSearchType::class, null, [
            'types' => $requestTypes,
            'collaborators' => $collaborators,
        ]);

        // Récupérer les valeurs des filtres depuis la requête
        $filterType = $request->query->get('type');
        $filterStart = $request->query->get('start');
        $filterEnd = $request->query->get('end');
        $filterNumber = $request->query->get('days');
        $filterAnswer = $request->query->get('status');

        $criteria = Criteria::create();

        if ($filterType) {
            $filterTypeObject = $requestTypeRepository->find($filterType);
            if ($filterTypeObject) {
                $criteria->andWhere(Criteria::expr()->eq('requestType', $filterTypeObject));
            }
        }
        if ($filterStart) {
            $startOfDay = (new \DateTimeImmutable($filterStart))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterStart))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('startAt', $startOfDay))
                    ->andWhere(Criteria::expr()->lte('startAt', $endOfDay));
        }
        if ($filterEnd) {
            $startOfDay = (new \DateTimeImmutable($filterEnd))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterEnd))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('endAt', $startOfDay))
                    ->andWhere(Criteria::expr()->lte('endAt', $endOfDay));
        }
        
        if ($filterAnswer) {
            $criteria->andWhere(Criteria::expr()->eq('answer', $filterAnswer));
        }

        $criteria->orderBy(['createdAt' => 'DESC']);

        if ('ROLE_COLLABORATOR' == $user->getRole()) {
            // PAGE COLLABORATEUR

            $criteria->andWhere(Criteria::expr()->eq('collaborator', $person));

            $filterDate = $request->query->get('requested');

            if ($filterDate) {
                $startOfDay = (new \DateTimeImmutable($filterDate))->setTime(0, 0, 0);
                $endOfDay = (new \DateTimeImmutable($filterDate))->setTime(23, 59, 59);
                $criteria->andWhere(Criteria::expr()->gte('createdAt', $startOfDay))
                        ->andWhere(Criteria::expr()->lte('createdAt', $endOfDay));
            }

            $requests = $requestRepository->matching($criteria);

            if ($filterNumber) {
                $requests = $requests->filter(function ($request) use ($filterNumber) {
                    return $request->getWorkingDays() == $filterNumber;
                });
            }

            $pagination = $paginator->paginate(
                $requests, /* query NOT result */
                $request->query->getInt('page', 1), /* page number */
                10 /* limit per page */
            );

            return $this->render('request/request_historic.html.twig', [
                'form' => $form,
                'requests' => $pagination,
                'filterType' => $filterType,
                'filterDate' => $filterDate,
                'filterStart' => $filterStart,
                'filterEnd' => $filterEnd,
                'filterNumber' => $filterNumber,
                'filterAnswer' => $filterAnswer,
            ]);
        } else {
            // PAGE MANAGER

            $criteria->andWhere(Criteria::expr()->in('collaborator', $collaborators));

            $filterCollaborator = $request->query->get('collaborator');

            if ($filterCollaborator) {
                $filterCollaboratorObject = $personRepository->find($filterCollaborator);
                if ($filterCollaboratorObject) {
                    $criteria->andWhere(Criteria::expr()->eq('collaborator', $filterCollaboratorObject));
                }
            }

            $requests = $requestRepository->matching($criteria);

            if ($filterNumber) {
                $requests = $requests->filter(function ($request) use ($filterNumber) {
                    return $request->getWorkingDays() == $filterNumber;
                });
            }

            $pagination = $paginator->paginate(
                $requests, /* query NOT result */
                $request->query->getInt('page', 1), /* page number */
                10 /* limit per page */
            );

            return $this->render('request/request_historic.html.twig', [
                'form' => $form,
                'requests' => $pagination,
                'filterType' => $filterType,
                'filterStart' => $filterStart,
                'filterEnd' => $filterEnd,
                'filterNumber' => $filterNumber,
                'filterAnswer' => $filterAnswer,
            ]);
        }
    }

    #[Route('/request/new', name: 'request_new', methods: ['POST', 'GET'])]
    public function request_new(HttpRequest $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $user->getPerson();

        $theRequest = new Request();
        $theRequest->setCollaborator($person);

        $form = $this->createForm(RequestForm::class, $theRequest, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (null != $form['file']->getData()) {
                $file = $form['file']->getData(); // On récupère le fichier téléchargé

                $firstName = strtolower($person->getFirstName()); // Assure que la méthode `getFirstName()` existe
                $lastName = strtolower($person->getLastName());   // Assure que la méthode `getLastName()` existe

                // Création d'un répertoire pour le nom et prénom
                $destination = $this->getParameter('kernel.project_dir') . '/public/files/' . $firstName . '_' . $lastName;
                if (!is_dir($destination)) {
                    mkdir($destination, 0777, true);
                }

                // Génération d'un identifiant unique pour le fichier
                $uniqueId = uniqid();

                // Ajout de la date au nom du fichier
                $date = (new \DateTime())->format('Y-m-d');

                // Renommer le fichier avec un format personnalisé
                $fileExtension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION); // Récupère l'extension du fichier
                $fileName = $firstName . '_' . $lastName . '_' . $date . '_' . $uniqueId . '.' . $fileExtension;

                // Déplacement du fichier dans le répertoire
                $file->move($destination, $fileName);

                // Enregistrement du nom du fichier dans l'entité
                $theRequest->setReceiptFile($fileName);
            } 

            if (null == $form['comment']->getData()) {
                $theRequest->setComment(null);
            }

            $currentDateTime = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));

            $theRequest->setCreatedAt($currentDateTime);
            $theRequest->setAnswer(Statut::EnCours->value);

            $entityManager->persist($theRequest);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Votre demande a été soumise avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de la demande.');
            }

            return $this->redirectToRoute('request_historic', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('request/request_new.html.twig', [
            'requete' => $theRequest,
            'form' => $form,
        ]);
    }

    #[Route('/request/show/{id}', name: 'request_show', methods: ['POST', 'GET'])]
    public function show(HttpRequest $httpRequest, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (! $user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }

        $person = $user->getPerson();

        $form = $this->createForm(AnswerType::class, $request, []);
        $form->handleRequest($httpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form['answerComment']->getData();
            $answerAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $answer = 3;
            $result = '';

            /** @var \Symfony\Component\Form\SubmitButton $approveButton */
            $approveButton = $form->get('approve');
            if ($approveButton->isClicked()) {
                $answer = 1;
                $result = 'validé';
            }

            /** @var \Symfony\Component\Form\SubmitButton $rejectButton */
            $rejectButton = $form->get('reject');
            if ($rejectButton->isClicked()) {
                $answer = 2;
                $result = 'refusé';
            }

            $request->setAnswer($answer);
            $request->setAnswerComment($comment);
            $request->setAnswerAt($answerAt);

            $entityManager->persist($request);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'La réponse a été enregistrée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de la réponse.');
            }
            
            // On récupère l'email du collaborateur
            $collaborator = $request->getCollaborator();
            $collaboratorUser = $entityManager->getRepository(User::class)->findOneBy(['person' => $collaborator]);
            $email = $collaboratorUser->getEmail();
            $alert = $collaborator->getAlertOnAnswer();

            if (true == $alert) {
                $to = $email;
                $subject = 'CongéFacile : Votre demande de congé à été ' . $result . 'e.';
                $message = '' . $person->getFirstName() . ' ' . $person->getLastName() . ' à ' . $result . ' votre demande de congé du ' . date_format($request->getCreatedAt(), 'd/m/Y') . '.';

                $this->mailerService->sendEmail($to, $subject, $message);
            }
        }

        if ('ROLE_COLLABORATOR' == $user->getRole()) {
            if ($request->getCollaborator()->getId() !== $person->getId()) {
                return $this->redirectToRoute('request_historic');
            }
        } else {
            if ($request->getCollaborator()->getManager()->getId() !== $person->getId()) {
                return $this->redirectToRoute('request_historic');
            }
        }

        return $this->render('request/request_show.html.twig', [
            'request' => $request,
            'form' => $form,
        ]);
    }

    #[Route('/request/pending', name: 'request_pending', methods: ['POST', 'GET'])]
    public function request_pending(HttpRequest $request, PersonRepository $personRepository, RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        if (! $user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }

        $manager = $user->getPerson();
        $collaborators = $personRepository->getPersonByManager($manager);
        $requestTypes = $requestTypeRepository->findAll();

        $form = $this->createForm(PendingRequestSearchType::class, null, [
            'types' => $requestTypes,
            'collaborators' => $collaborators,
        ]);

        // Récupérer les valeurs des filtres depuis la requête
        $filterType = $request->query->get('type');
        $filterDate = $request->query->get('requested');
        $filterStart = $request->query->get('start');
        $filterEnd = $request->query->get('end');
        $filterNumber = $request->query->get('days');
        $filterCollaborator = $request->query->get('collaborator');

        $criteria = Criteria::create();

        if ($filterType) {
            $filterTypeObject = $requestTypeRepository->find($filterType);
            if ($filterTypeObject) {
                $criteria->andWhere(Criteria::expr()->eq('requestType', $filterTypeObject));
            }
        }
        if ($filterStart) {
            $startOfDay = (new \DateTimeImmutable($filterStart))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterStart))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('startAt', $startOfDay))
                    ->andWhere(Criteria::expr()->lte('startAt', $endOfDay));
        }
        if ($filterEnd) {
            $startOfDay = (new \DateTimeImmutable($filterEnd))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterEnd))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('endAt', $startOfDay))
                    ->andWhere(Criteria::expr()->lte('endAt', $endOfDay));
        }
        
        $criteria->andWhere(Criteria::expr()->eq('answer', 3));

        if ($filterDate) {
            $startOfDay = (new \DateTimeImmutable($filterDate))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterDate))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('createdAt', $startOfDay))
                    ->andWhere(Criteria::expr()->lte('createdAt', $endOfDay));
        }

        if ($filterCollaborator) {
            $filterCollaboratorObject = $personRepository->find($filterCollaborator);
            if ($filterCollaboratorObject) {
                $criteria->andWhere(Criteria::expr()->eq('collaborator', $filterCollaboratorObject));
            }
        }

        $requests = $requestRepository->matching($criteria);

        if ($filterNumber) {
            $requests = $requests->filter(function ($request) use ($filterNumber) {
                return $request->getWorkingDays() == $filterNumber;
            });
        }



        $pagination = $paginator->paginate(
            $requests, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit par page */
        );

        return $this->render('request/request_pending.html.twig', [
            'requests' => $pagination,
            'form' => $form,
            'filterType' => $filterType,
            'filterDate' => $filterDate,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'filterNumber' => $filterNumber,
            'filterCollaborator' => $filterCollaborator,
        ]);
    }

    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function statistic(RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository): Response
    {
        // 1er graphique : Nombre de demandes par type de demande
        $requestTypes = $requestTypeRepository->findAll();
        $countRequest = [];

        // On compte le nombre de demandes par type de demande
        foreach ($requestTypes as $type) {
            $countRequest[$type->getName()] = $requestRepository->countRequestsByRequestType($type);
        }

        // 2ème graphique : Pourcentage d'acceptation des demandes sur l'année

        // On initialise le tableau pour stocker les pourcentages d'acceptation
        $acceptancePercentage = [];

        // On parcourt les mois de l'année
        for ($number = 1; $number <= 12; ++$number) {
            // On crée un objet DateTime pour le mois en cours
            $month = new \DateTime();
            $month->setDate((int)date('Y'), $number, 1);

            // On récupère les demandes du mois en cours
            $requests = $requestRepository->findRequestsByMonthOfAnswer($month);//revoir

            // On initialise les compteurs d'acceptation et de refus
            $acceptance = 0;
            $refusal = 0;

            // On parcourt les demandes et on compte les acceptations et refus
            foreach ($requests as $request) {
                if ('Accepté' == $request->getAnswer()->label()) {
                    ++$acceptance;
                } elseif ('Refusé' == $request->getAnswer()->label()) {
                    ++$refusal;
                }
            }

            // On calcule le pourcentage d'acceptation
            if (0 == $acceptance && 0 == $refusal) {
                // Si aucune demande n'a été faite, on met le pourcentage à null
                $acceptancePercentage[$number] = null;
            } else {
                // On évite la division par zéro
                $percent = $acceptance * 100 / ($acceptance + $refusal);
                $acceptancePercentage[$number] = $percent;
            }
        }

        $startDate = new \DateTime('first day of January this year');
        $endDate = new \DateTime('last day of December this year');

        // On récupère les demandes groupées par mois
        $requestsGroupedByMonth = $requestRepository->findRequestsGroupedByMonth($startDate, $endDate);

        return $this->render('request/request_statistics.html.twig', [
            'requestTypes' => $requestTypes,
            'countRequest' => $countRequest,
            'acceptancePercentage' => $acceptancePercentage,
            'requestsGroupedByMonth' => $requestsGroupedByMonth,
        ]);
    }
}
