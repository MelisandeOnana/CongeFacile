<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnectionController extends AbstractController
{
    private MailerService $mailerService;

    private ParameterBagInterface $params;

    public function __construct(MailerService $mailerService, ParameterBagInterface $params)
    {
        $this->mailerService = $mailerService;
        $this->params = $params;
    }

    #[Route('/connexion', name: 'login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
            return $this->redirectToRoute('home_index');
        }
        // Personnaliser le message d'erreur
        $errorMessage = null;
        if ($error) {
            if ('Disabled account.' === $error->getMessageKey()) {
                $errorMessage = 'Ce compte a été désactivé.';
            } else {
                $errorMessage = 'Identifiants invalides. Veuillez vérifier votre adresse e-mail et votre mot de passe.';
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $errorMessage,
        ]);
    }

    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->redirectToRoute('login');
        }
        $person = $user->getPerson();

        return $this->render('default/index.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Ce contrôleur peut être vide : il ne sera jamais exécuté !
        throw new \Exception('N\'oubliez pas d\'activer la déconnexion dans security.yaml');
    }

    #[Route('/forgotpassword', name: 'forgot_password', methods: ['GET'])]
    public function forgot_password(Request $request, UserRepository $repository): Response
    {
        $result = '';
        $email = $request->query->get('email');

        if ($email) {
            $user = $repository->findByEmail(htmlspecialchars($email));
            if ($user) {
                $result = $this->sendPasswordResetRequest($user, $email);
            } else {
                $result = 'Email incorrect';
            }
        }

        return $this->render('security/forgot_password.html.twig', ['result' => $result]);
    }

    private function sendPasswordResetRequest($user, $email): string
    {
        $to = $this->params->get('mailer_contact_email');
        $subject = sprintf(
            'CongéFacile : %s %s demande un changement de mot de passe.',
            $user->getPerson()->getFirstName(),
            $user->getPerson()->getLastName()
        );
        $message = sprintf(
            '%s %s demande un changement de mot de passe.<br>Adresse email de la personne : %s.<br><br>Après changement, merci de notifier l’utilisateur de son nouveau mot de passe.',
            $user->getPerson()->getFirstName(),
            $user->getPerson()->getLastName(),
            $email
        );

        try {
            $this->mailerService->sendEmail($to, $subject, $message);
            return 'Demande envoyée';
        } catch (\Exception $e) {
            return 'Demande non envoyée';
        }
    }
}
