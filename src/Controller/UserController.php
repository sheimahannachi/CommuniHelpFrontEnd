<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/medcin/add', name: 'add_medcin')]
    public function addMedcin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérez ici la logique de sauvegarde des données
    
            // Persistez l'entité User dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_connect');
        }
    
        return $this->render('user/addmedcin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // Ajoutez des fonctions similaires pour les autres types d'utilisateurs

    #[Route('/Benvole/add', name: 'add_Benevole')]
    public function addBenevole(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données depuis la requête
        $user = new User();
    
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
                ]])
            ->add('Status')
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérez ici la logique de sauvegarde des données
    
            // Persistez l'entité User dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_connect');
        }
    
        // Retourner une réponse de succès ou rediriger vers une autre page
        return $this->render('user/addbenevole.html.twig', [
            'form' => $form->createView(),
        ]); // // Redirection vers la page d'accueil par exemple
    }
    
    #[Route('/Association/add', name: 'add_association')]
    public function addAssociation(Request $request, EntityManagerInterface $entityManager): Response
    {// Récupérer les données depuis la requête
        $user = new User();
    
        $form = $this->createFormBuilder($user)
            ->add('email')
            ->add('nom')
            
            ->add('password', PasswordType::class)
            ->add('adresse')
            ->add('num_tel')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                'ROLE_ASSOCIATION' => 'ROLE_ASSOCIATION', // Valeur par défaut pour ROLE_BENEVOLE
                ]])
            ->add('Status')
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérez ici la logique de sauvegarde des données
    
            // Persistez l'entité User dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_connect');
        }
    
        // Retourner une réponse de succès ou rediriger vers une autre page
        return $this->render('user/addassociation.html.twig', [
            'form' => $form->createView(),
        ]); // // Redirection vers la page d'accueil par exemple
    }
}



