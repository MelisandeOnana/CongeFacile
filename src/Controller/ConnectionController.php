<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
class ConnectionController extends AbstractController
{
    #[Route('/', name: 'login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername() ?? '';

        if ($this->getUser()) {
            return $this->redirectToRoute('home_index');
        }
        // Personnaliser le message d'erreur
        $errorMessage = null;
        if ($error) {
           $errorMessage = 'Identifiants invalides. Veuillez vérifier votre adresse e-mail et votre mot de passe.';
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $errorMessage,
        ]);
    }

    #[Route('/home', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $user->getPerson();

        return $this->render('default/home.html.twig', [
            'person' => $person,
        ]);
    }


    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Ce contrôleur peut être vide : il ne sera jamais exécuté !
        throw new Exception('N\'oubliez pas d\'activer la déconnexion dans security.yaml');
    }


    #[Route('/forgotpassword', name: 'forgot_password', methods: ['GET'])]
    public function forgot_password(UserRepository $repository, User $user = null): Response
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
                
                try{
                    mail($to, $subject, $message);
                    $reussi = "Demande envoyée";
                    return $this->render('security/forgot_password.html.twig', ["reussi" => $reussi]);
                }catch(Exception $e){
                    $reussi = "Demande non envoyée";
                    return $this->render('security/forgot_password.html.twig', ["reussi" => $reussi]);
                }
            }else{
                $reussi = "Email incorrect";
                return $this->render('security/forgot_password.html.twig', ["reussi" => $reussi]);
            }
        }

        return $this->render('security/forgot_password.html.twig', ["reussi" => ""]);
    }
}
