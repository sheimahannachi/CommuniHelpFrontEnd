<?php

namespace App\Controller;
use App\Entity\Test;
use App\Form\TestType;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/find', name: 'app_find')]
    public function affiche(TestRepository $repository)
    {
        $event=$repository->findAll();
        return $this->render('test/affiche.html.twig',['event'=>$event]);

    }
    #[Route('/add', name: 'app_add')]
    public function Add(Request $request)
    {
        $event = new Test();
        $form = $this->createForm(TestType::class, $event);
        $form->add('ajouter', SubmitType::class, [
            'label' => 'Ajouter',
            'attr' => ['class' => 'btn btn-primary'],
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérez le téléchargement du fichier
            $file = $form->get('path')->getData();
            if ($file) {
                // Générez un nom de fichier unique
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                // Déplacez le fichier vers le répertoire où vous souhaitez le stocker
                $file->move(
                    $this->getParameter('image_directory'), // Le répertoire de destination
                    $fileName // Le nom de fichier unique
                );
                // Enregistrez le chemin du fichier dans le champ "path" de l'entité Test
                $event->setPath($fileName);
            }
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
    
            return $this->redirectToRoute('app_find');
        }
    
        return $this->render('test/add.html.twig', ['f' => $form->createView()]);
    }
    
    #[Route('/delete/{id}', name: 'app_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $test = $entityManager->getRepository(Test::class)->find($id);
    
        if (!$test) {
            throw $this->createNotFoundException('Aucun test trouvé pour l\'ID : ' . $id);
        }
    
        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $entityManager->remove($test);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_find', [], Response::HTTP_SEE_OTHER);
    }
    
    

   
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Test $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($event)
            ->add('nom', TextType::class)
            ->add('lieu', TextType::class)
            ->add('date', TextType::class)
            ->add('ajouter', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisez le gestionnaire d'entités pour persister et enregistrer les modifications
            $entityManager->persist($event);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_find', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('test/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }}