<?php

namespace App\Controller;
use App\Repository\ParticipantsRepository;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserEventController extends AbstractController
{
    #[Route('/findU', name: 'app_findU')]
    public function affiche(TestRepository $testRepository, ParticipantsRepository $participantsRepository,SessionInterface $session)
    {
        $events = $testRepository->findAll();
        $participantsCounts = [];
        $user = $session->get('user');

        foreach ($events as $event) {
            $id_ev = $event->getId();
            $participantsCount = $participantsRepository->countParticipantsPerTest($id_ev);
            $participantsCounts[$id_ev] = $participantsCount;
        }

        return $this->render('user_event/affiche.html.twig', [
            'event' => $events,
            'participantsCounts' => $participantsCounts,
            'user'=>$user,
        ]);
    }
    #[Route('/Benevole/edit/{id}', name: 'edit_benevole')]
    public function editBenevole(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createFormBuilder($user)
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('password')
            ->add('adresse')
            ->add('num_tel')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_BENEVOLE' => 'ROLE_BENEVOLE', // Valeur par défaut pour ROLE_BENEVOLE
                ]
            ])
            ->add('Status')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérez ici la logique de sauvegarde des données modifiées
            $entityManager->flush();

            return $this->redirectToRoute('app_connect');
        }

        return $this->render('user/editbenevole.html.twig', [
            'form' => $form->createView(),
            'user'=>$user,
        ]);
    }
}