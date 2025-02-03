<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use App\Entity\User;
use App\Form\RequestForm;
use App\Entity\Request;
use App\Repository\RequestRepository;
use App\Repository\RequestTypeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\PersonRepository;

class RequestController extends AbstractController
{

    #[Route('/request/historic', name: 'request_historic', methods: ['GET'])]
    public function request_historic(HttpRequest $request, RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $user->getPerson();

        // Récupérer les valeurs des filtres depuis la requête
        $filterType = $request->query->get('type');
        $filterDate = $request->query->get('requested');
        $filterStart = $request->query->get('start');
        $filterEnd = $request->query->get('end');
        $filterNumber = $request->query->get('days');
        $filterAnswer = $request->query->get('status');

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('collaborator', $person));
        
        if ($filterType) {
            $filterTypeObject = $requestTypeRepository->find($filterType);
            if ($filterTypeObject) {
                $criteria->andWhere(Criteria::expr()->eq('requestType', $filterTypeObject));
            }
        }
        
        if ($filterDate) {
            $startOfDay = (new \DateTimeImmutable($filterDate))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterDate))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('createdAt', $startOfDay))
                     ->andWhere(Criteria::expr()->lte('createdAt', $endOfDay));
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
            $requests = $requestRepository->findAll();
            $matchingRequests = [];

            foreach ($requests as $req) {
                if ($req->getWorkingdays() == $filterNumber) {
                    $matchingRequests[] = $req;
                }
            }

            $criteria->andWhere(Criteria::expr()->in('id', array_map(function($req) {
                return $req->getId();
            }, $matchingRequests)));
        }
        if ($filterAnswer) {
            $criteria->andWhere(Criteria::expr()->eq('answer', $filterAnswer));
        }
    
        // Rechercher les requêtes en fonction des critères
        $criteria->orderBy(['createdAt' => 'DESC']);
        $requests = $requestRepository->matching($criteria);

        $requestTypes = $requestTypeRepository->findAll();

        $pagination = $paginator->paginate(
            $requests, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('default/request/request_historic.html.twig', [
            'requests' => $pagination,
            'requestTypes' => $requestTypes,
            'filterType' => $filterType,
            'filterDate' => $filterDate,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'filterNumber' => $filterNumber,
            'filterAnswer' => $filterAnswer,
        ]);
    }
    
    #[Route('/request/new', name: 'request_new', methods: ['POST','GET'])]
    public function request_new(HttpRequest $request, EntityManagerInterface $entityManager): Response
    {
        $theRequest = new Request();
        $form = $this->createForm(RequestForm::class, $theRequest, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new Exception('L\'utilisateur n\'est pas connecté.');
            }

            $person = $user->getPerson();

            if ($form['fichier']->getData() != null) {

                $file = $form['fichier']->getData(); // On récupère le fichier téléchargé
                $destination = $this->getParameter('kernel.project_dir') . '/public/files';

                $personId = $person->getId();

                // Compter le nombre de demandes déjà crée par la personne pour créer un id unique pour le fichier
                $requestCount = $entityManager->getRepository(Request::class)->count(['collaborator' => $person]);
                $idfile = $requestCount + 1;


                // Générer un nom unique pour le fichier
                $fileName = $personId . '-' . $idfile . ' ' . $file->getClientOriginalName();
                try {
                    $file->move($destination, $fileName);
                    $this->addFlash('success', 'Fichier téléchargé avec succès !');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                }
            } else {
                $fileName = "";
            }

            $currentDateTime = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $answerAt = new \DateTimeImmutable('1970-01-01 00:00:00');

            $theRequest->setReceiptFile($fileName);
            $theRequest->setCollaborator($person);
            $theRequest->setCreatedAt($currentDateTime);
            $theRequest->setAnswerComment("");
            $theRequest->setAnswer(3); // 3 = En cours 
            $theRequest->setAnswerAt($answerAt);

            $entityManager->persist($theRequest);
            $entityManager->flush();

            $this->addFlash('success', 'Requete créé avec succès.');

            return $this->redirectToRoute('request_historic', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('default/request/request_new.html.twig', [
            'requete' => $theRequest,
            'form' => $form,
        ]);
    }

    #[Route('/request/show/{id}', name: 'request_show', methods: ['POST','GET'])]
    public function show(HttpRequest $request, Request $requete, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas connecté.');
        }

        $person = $user->getPerson();

        if ($requete->getCollaborator()->getId() !== $person->getId()) {
            return $this->redirectToRoute('request_historic');
        }

        return $this->render('default/request/request_show.html.twig', [
            'request' => $requete,
        ]);
    }
 
    #[Route('/request_pending', name: 'request_pending', methods: ['POST','GET'])]
    public function request_pending(HttpRequest $request,PersonRepository $personRepository, RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas connecté.');
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

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->in('collaborator',$collaborators));
        $criteria->andWhere(Criteria::expr()->eq('answer', "3"));

        
        if ($filterType) {
            $filterTypeObject = $requestTypeRepository->find($filterType);
            if ($filterTypeObject) {
                $criteria->andWhere(Criteria::expr()->eq('requestType', $filterTypeObject));
            }
        }
        if($filterCollaborator){
            $filterCollaboratorObject = $personRepository->find($filterCollaborator);
            if ($filterCollaboratorObject) {
                $criteria->andWhere(Criteria::expr()->eq('collaborator', $filterCollaboratorObject));
            }
        }
        
        if ($filterDate) {
            $startOfDay = (new \DateTimeImmutable($filterDate))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterDate))->setTime(23, 59, 59);
            $criteria->andWhere(Criteria::expr()->gte('createdAt', $startOfDay))
                     ->andWhere(Criteria::expr()->lte('createdAt', $endOfDay));
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
            $requests = $requestRepository->findAll();
            $matchingRequests = [];

            foreach ($requests as $req) {
                if ($req->getWorkingdays() == $filterNumber) {
                    $matchingRequests[] = $req;
                }
            }

            $criteria->andWhere(Criteria::expr()->in('id', array_map(function($req) {
                return $req->getId();
            }, $matchingRequests)));
        }

    
        // Rechercher les requêtes en fonction des critères
        $criteria->orderBy(['createdAt' => 'DESC']);
        $requests = $requestRepository->matching($criteria);

        $requestTypes = $requestTypeRepository->findAll();

        $pagination = $paginator->paginate(
            $requests, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('default/request/request_pending.html.twig', [
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
}