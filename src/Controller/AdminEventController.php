<?php

namespace App\Controller;

use Symfony\Component\Form\FormTypeInterface;
use App\Entity\Test;
use App\Repository\ParticipantsRepository;
use App\Repository\TestRepository;
use App\Entity\Participants;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AdminEventController extends AbstractController
{
    #[Route('/admin/event', name: 'app_admin_event')]
    public function index(): Response
    {
        return $this->render('admin_event/index.html.twig', [
            'controller_name' => 'AdminEventController',
        ]);
    }
    #[Route('/findev_ad', name: 'app_findadmin')]
    public function affiche(TestRepository $repository)
    {
        $ev=$repository->findAll();
        return $this->render('test/admin_ev/affiche_ev_admin.html.twig',['ev'=>$ev]);

    }
   
   
    #[Route('/{id}/editad', name: 'app_event_editad', methods: ['GET', 'POST'])]
public function edit(Request $request, Test $event, EntityManagerInterface $entityManager): Response
{
    $form = $this->createFormBuilder($event)
        ->add('nom', TextType::class)
        ->add('lieu', TextType::class)
        ->add('date', TextType::class)
       
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Utilisez le gestionnaire d'entités pour persister et enregistrer les modifications
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_findadmin', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('test/admin_ev/edit_ev_admin.html.twig', [
        'event' => $event,
        'form' => $form->createView(),
    ]);
}
#[Route('/admin/delete/{id}', name: 'admin_delete')]
    public function deleteTest(EntityManagerInterface $entityManager, int $id): Response
    {
        $testRepository = $entityManager->getRepository(Test::class);
        $test = $testRepository->find($id);

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        // Supprimer le test
        $entityManager->remove($test);
        $entityManager->flush();

        return $this->redirectToRoute('app_findadmin'); // Redirigez vers une route après la suppression
    }
    #[Route('/findpar', name: 'app_findpar')]
    public function aff(ParticipantsRepository $repository)
    {
        $part=$repository->findAll();
        return $this->render('participants/affiche.html.twig',['p'=>$part]);

    }

}