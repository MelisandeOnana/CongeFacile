<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailerService;
use App\Service\PasswordResetService;
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
    public function forgot_password(Request $request, UserRepository $repository, PasswordResetService $passwordResetService): Response
    {
        $result = '';
        $email = $request->query->get('email');

        if ($email) {
            $user = $repository->findByEmail(htmlspecialchars($email));
            if ($user) {
                $result = $passwordResetService->sendPasswordResetRequest($user, $email);
            } else {
                $result = 'Email incorrect';
            }
        }

        return $this->render('security/forgot_password.html.twig', ['result' => $result]);
    }
}
