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

            $user = $this->getUser()->getPerson();
            $currentDateTime = new \DateTimeImmutable();
            $answerAt = new \DateTimeImmutable("00-00-0000");

            $theRequest->setCollaborator($user);
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

        return $this->render('request/new.html.twig', [
            'requete' => $theRequest,
            'form' => $form,
        ]);
    }
    #[Route('/historic', name: 'historic', methods: ['GET'])]
    public function historic(RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas de type User.');
        }
        $person = $user->getPerson();
        $requests = $requestRepository->findBy(['collaborator' => $person]);

        //Filtres
        $requestType = $requestTypeRepository->findAll();


        return $this->render('default/historic.html.twig', [
            'requests' => $requests,
            'requestTypes' => $requestType,
        ]);
    }
}