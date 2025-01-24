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

class RequestController extends AbstractController
{
    #[Route('/request/new', name: 'request_new', methods: ['POST','GET'])]
    public function request_new(HttpRequest $request, EntityManagerInterface $entityManager): Response
    {
        $theRequest = new Request();
        $form = $this->createForm(RequestForm::class, $theRequest, [
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new Exception('L\'utilisateur n\'est pas de type User.');
            }
            $person = $user->getPerson();
            $currentDateTime = new \DateTimeImmutable();
            $answerAt = new \DateTimeImmutable("00-00-0000");

            $theRequest->setCollaborator($person);
            $theRequest->setCreatedAt($currentDateTime);
            $theRequest->setAnswerComment("");
            $theRequest->setAnswer(0);
            $theRequest->setAnswerAt($answerAt);
            $theRequest->setReceiptFile("");

            $entityManager->persist($theRequest);
            $entityManager->flush();

            $this->addFlash('success', 'Requete créé avec succès.');

            return $this->redirectToRoute('historic', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('default/request/new.html.twig', [
            'requete' => $theRequest,
            'form' => $form,
        ]);
    }
 
    #[Route('/historic', name: 'historic', methods: ['GET'])]
    public function historic(HttpRequest $request, RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas de type User.');
        }
        $person = $user->getPerson();

        // Récupérer les valeurs des filtres depuis la requête
        $filterType = $request->query->get('type');
        $filterDate = $request->query->get('requested');
        $filterStart = $request->query->get('start');
        $filterEnd = $request->query->get('end');
        $filterNumber = $request->query->get('number');
        $filterAnswer = $request->query->get('status');

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('collaborator', $person));
        
        if ($filterType) {
            $criteria->andWhere(Criteria::expr()->eq('requestType', $filterType));
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
            $criteria->andWhere(Criteria::expr()->eq('requestNumber', $filterNumber));
        }
        if ($filterAnswer) {
            $criteria->andWhere(Criteria::expr()->eq('answer', $filterAnswer));
        }
    
        // Rechercher les requêtes en fonction des critères
        $requests = $requestRepository->matching($criteria);

        $requestTypes = $requestTypeRepository->findAll();

        return $this->render('default/historic.html.twig', [
            'requests' => $requests,
            'requestTypes' => $requestTypes,
            'filterType' => $filterType,
            'filterDate' => $filterDate,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'filterNumber' => $filterNumber,
            'filterAnswer' => $filterAnswer,
        ]);
    }
}