<?php

namespace App\Controller;

use App\Entity\Request;
use App\Entity\User;
use App\Enum\Statut;
use App\Form\AnswerType;
use App\Form\RequestForm;
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

        // Récupérer les valeurs des filtres depuis la requête
        $filterType = $request->query->get('type');
        $filterStart = $request->query->get('start');
        $filterEnd = $request->query->get('end');
        $filterNumber = $request->query->get('days');
        $filterAnswer = $request->query->get('status');

        $requestTypes = $requestTypeRepository->findAll();

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
        if ($filterNumber) {
            $criteria->andWhere(Criteria::expr()->eq('workingdays', $filterNumber));
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

            $pagination = $paginator->paginate(
                $requests, /* query NOT result */
                $request->query->getInt('page', 1), /* page number */
                10 /* limit per page */
            );

            return $this->render('request/request_historic.html.twig', [
                'requests' => $pagination,
                'requestTypes' => $requestTypes,
                'filterType' => $filterType,
                'filterDate' => $filterDate,
                'filterStart' => $filterStart,
                'filterEnd' => $filterEnd,
                'filterNumber' => $filterNumber,
                'filterAnswer' => $filterAnswer,
            ]);
        } else {
            // PAGE MANAGER

            // Récupérer les collaborateurs du manager
            $collaborators = $personRepository->getPersonByManager($person);

            $criteria->andWhere(Criteria::expr()->in('collaborator', $collaborators));

            $filterCollaborator = $request->query->get('collaborator');

            if ($filterCollaborator) {
                $filterCollaboratorObject = $personRepository->find($filterCollaborator);
                if ($filterCollaboratorObject) {
                    $criteria->andWhere(Criteria::expr()->eq('collaborator', $filterCollaboratorObject));
                }
            }

            $requests = $requestRepository->matching($criteria);

            $pagination = $paginator->paginate(
                $requests, /* query NOT result */
                $request->query->getInt('page', 1), /* page number */
                10 /* limit per page */
            );

            return $this->render('request/request_historic.html.twig', [
                'requests' => $pagination,
                'requestTypes' => $requestTypes,
                'collaborators' => $collaborators,
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
                $destination = $this->getParameter('kernel.project_dir') . '/public/files';

                $personId = $person->getId();

                // Compter le nombre de demandes déjà crée par la personne pour créer un id unique pour le fichier
                $requestCount = $entityManager->getRepository(Request::class)->count(['collaborator' => $person]);
                $idfile = $requestCount + 1;

                // Générer un nom unique pour le fichier
                $fileName = $personId . '-' . $idfile . '-' . $file->getClientOriginalName();
                try {
                    $file->move($destination, $fileName);
                    $theRequest->setReceiptFile($fileName);
                    $this->addFlash('success', 'Fichier téléchargé avec succès !');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                }
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
                $this->addFlash('success', 'Requête créé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de la requête.');
            }

            // Envoi d'un email au manager

            $manager = $person->getManager();
            $managerUser = $entityManager->getRepository(User::class)->findOneBy(['person' => $manager]);
            $emailManager = $managerUser->getEmail();
            $alert = $manager->getAlertNewRequest();

            if (true == $alert) {
                $to = $emailManager;
                $subject = 'CongéFacile : Nouvelle demande de congé déposée';
                $message = '' . $person->getFirstName() . ' ' . $person->getLastName() . ' à déposé une demande de congé.<br>
                Merci de vous connecter à votre espace pour valider ou refuser la demande.';

                $this->mailerService->sendEmail($to, $subject, $message);
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

            // Envoi d'un email au collaborateur

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

        // Récupérer les valeurs des filtres depuis la requête
        $filterType = $request->query->get('type');
        $filterDate = $request->query->get('requested');
        $filterStart = $request->query->get('start');
        $filterEnd = $request->query->get('end');
        $filterNumber = $request->query->get('days');
        $filterCollaborator = $request->query->get('collaborator');

        // Utiliser la méthode du repository pour obtenir les requêtes filtrées
        $requests = $requestRepository->findFilteredRequests($collaborators, $filterType, $filterDate, $filterStart, $filterEnd, $filterNumber, $filterCollaborator);

        $requestTypes = $requestTypeRepository->findAll();

        $pagination = $paginator->paginate(
            $requests, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            6 /* limit par page */
        );

        return $this->render('request/request_pending.html.twig', [
            'requests' => $pagination,
            'requestTypes' => $requestTypes,
            'collaborators' => $collaborators,
            'filterType' => $filterType,
            'filterDate' => $filterDate,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'filterNumber' => $filterNumber,
        ]);
    }

    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function statistic(RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository): Response
    {
        // 1er graphique : Nombre de demandes par type de demande
        $requestTypes = $requestTypeRepository->findAll();
        $countRequest = [];

        foreach ($requestTypes as $type) {
            $countRequest[$type->getName()] = $requestRepository->countRequestsByRequestType($type);
        }

        // 2ème graphique : Pourcentage d'acceptation des demandes sur l'année

        $acceptancePercentage = [];

        for ($number = 1; $number <= 12; ++$number) {
            $month = new \DateTime();
            $month->setDate((int)date('Y'), $number, 1);

            $requests = $requestRepository->findRequestsByMonthOfAnswer($month);//revoir

            $acceptance = 0;
            $refusal = 0;

            foreach ($requests as $request) {
                if ('Accepté' == $request->getAnswer()->label()) {
                    ++$acceptance;
                } elseif ('Refusé' == $request->getAnswer()->label()) {
                    ++$refusal;
                }
            }

            if (0 == $acceptance && 0 == $refusal) {
                $acceptancePercentage[$number] = null;
            } else {
                $percent = $acceptance * 100 / ($acceptance + $refusal);
                $acceptancePercentage[$number] = $percent;
            }
        }

        $startDate = new \DateTime('first day of January this year');
        $endDate = new \DateTime('last day of December this year');

        $requestsGroupedByMonth = $requestRepository->findRequestsGroupedByMonth($startDate, $endDate);

        $acceptance = 0;
        $refusal = 0;

        foreach ($requestsGroupedByMonth as $result) {
            // On peut traiter les résultats ici
            // Par exemple, pour chaque mois, on peut compter les acceptations et les refus
            
            $month = $result['month'];
            //dd($month);
            $requestCount = $result['requestCount'];
            // Traitement des résultats selon les besoins
        }

        return $this->render('request/request_statistics.html.twig', [
            'requestTypes' => $requestTypes,
            'countRequest' => $countRequest,
            'acceptancePercentage' => $acceptancePercentage,
            'requestsGroupedByMonth' => $requestsGroupedByMonth,
        ]);
    }
}
