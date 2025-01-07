<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;

class ConnectionController extends AbstractController
{
    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('default/connexion.html.twig');
    }

    #[Route('/MotDePasseOublie', name: 'motdepasseoublie', methods: ['GET'])]
    public function motdepasseoublie(UserRepository $repository, User $user = null): Response
    {
        if(isset($_GET['email']) && !empty($_GET['email'])){

            $email = htmlspecialchars($_GET['email']);
            $user = $repository->findByEmail($email);
            
            if($user != null){

                $to = "dauguet.mathis@gmail.com";
                $subject = "CongéFacile :".$user->getPerson()->getFirstName()." ".$user->getPerson()->getLastName()." demande un changement de mot de passe.";
                $message = "".$user->getPerson()->getFirstName()." ".$user->getPerson()->getLastName()." demande un changement de mot de passe.
                Adresse email de la personne : “".$email."”.<br>
                Après changement, merci de notier l’utilisateur de son nouveau mot de passe.";
                
                if(mail($to, $subject, $message)){
                    $reussi = "Demande envoyée";
                    return $this->render('default/motdepasseoublie.html.twig', ["reussi" => $reussi]);
                }else{
                    $reussi = "Demande non envoyée";
                    return $this->render('default/motdepasseoublie.html.twig', ["reussi" => $reussi]);
                }
            }
        }

        return $this->render('default/motdepasseoublie.html.twig', ["reussi" => ""]);
    }
}
