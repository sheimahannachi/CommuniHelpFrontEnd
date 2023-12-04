<?php

namespace App\Controller;

use App\Entity\Participants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginType;
use App\Repository\ParticipantsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TestRepository;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/login.html.twig');
    }

    #[Route('/connect', name: 'app_connect')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $form = $this->createForm(LoginType::class);


        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),

        ]);
    }
    #[Route('/check-role', name: 'check_role')]
    public function checkRole(
        Request $request,
        UserRepository $repo,
        UtilisateurService $serviceMail,
        SessionInterface $session,
        TestRepository $testrepo,
        ParticipantsRepository $PRepo,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $user = $repo->findOneBy(['email' => $email]);

            if ($user && $passwordHasher->isPasswordValid($user, $password)) {
                $session->set('user', $user);
                $subject = "Your Login Information";
                $message = "Your email: " . $email . "\n" .
                    "Your password: " . $password;

                // Envoi de l'e-mail avec les informations de connexion
                $serviceMail->sendEmail($subject, $message, $email);

                // Reste du code pour rediriger en fonction du rôle de l'utilisateur
                if ($user->getRoles() === "ROLE_BENEVOLE") {
                    $events = $testrepo->findAll();
                    $participantsCounts = [];

                    foreach ($events as $event) {
                        $id_ev = $event->getId();
                        $participantsCount = $PRepo->countParticipantsPerTest($id_ev);
                        $participantsCounts[$id_ev] = $participantsCount;
                    }

                    return $this->render('user_event/affiche.html.twig', [
                        'event' => $events,
                        'participantsCounts' => $participantsCounts,
                        'user' => $user,
                    ]);
                } elseif ($user->getRoles() === "ROLE_ASSOCIATION") {
                    $events = $testrepo->findAll();

                    return $this->render('test/affiche.html.twig', [
                        'event' => $events,
                        'user' => $user,
                    ]);
                } elseif ($user->getRoles() === "ROLE_MEDCIN") {

                    return $this->redirectToRoute('add_Pub');

                }  elseif ($user->getRoles() === "ROLE_ADMIN") {
                    // Si l'utilisateur a le rôle ROLE_ADMIN, rediriger vers la page 'find_ad'
                    $events = $testrepo->findAll();
                }
                return $this->render('test/admin_ev/affiche_ev_admin.html.twig', [
                    'ev' => $events,
                    'user' => $user,]);

            } else {
                // Si l'utilisateur ou le mot de passe est incorrect, afficher un message d'erreur
                $this->addFlash('error', 'Adresse e-mail ou mot de passe incorrect.');
                return $this->redirectToRoute('app_connect');
            }
        }

        return $this->redirectToRoute('app_connect');
    }

    #[Route('/sendemailMembers', name: 'sendEmailToAllMembers')]
    public function sendEmail(Request $request, UtilisateurService $serviceMail): Response
    {
        $serviceMail->sendEmail('Test1 ',  "waaaaaa", 'nadhir.tebini@esprit.tn');

        return $this->redirectToRoute('app_connect');
    }
    #[Route('/logout', name: 'app_logout')]



    public function logout(
        Request $request,
        TokenStorageInterface $tokenStorage
    ): RedirectResponse {
        // Gérez la déconnexion de l'utilisateur ici
        $tokenStorage->setToken(null);

        // Redirigez l'utilisateur vers une page après la déconnexion
        return new RedirectResponse($this->generateUrl('app_connect'));
    }
}