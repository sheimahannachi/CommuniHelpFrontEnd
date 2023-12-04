<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PublicationsRepository;
use App\Form\AjoutPubType;
use App\Form\AjoutPubTypeAdmin;
use App\Entity\Publications;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\JsonResponse;




class PublicationsController extends AbstractController
{
    #[Route('/publications', name: 'app_publications')]
    public function index(): Response
    {
        return $this->render('publications/index.html.twig', [
            'controller_name' => 'PublicationsController',
        ]);
    }
    #[Route('/addPub', name: 'add_Pub')]
    public function addPub(ManagerRegistry $manager,EntityManagerInterface $entityManager, Request $request ,SessionInterface $session): Response
    {
        $em = $manager->getManager();
        $publication = new Publications();
    
        $form = $this->createForm(AjoutPubType::class, $publication);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
    
            if ($imageFile instanceof UploadedFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'.'.$imageFile->guessExtension();
    
                // Move the file to the directory where your images are stored
                $destinationDirectory = $this->getParameter('kernel.project_dir') . '/public/images_pub/';
                $imageFile->move($destinationDirectory, $newFilename);
    
                // Store the file name in the database
                $publication->setImagePath($newFilename);

                 // Use the getUser() method to retrieve the current user
                //** @var User $user */
               // $user = $this->getUser();

                // Add the current user's ID directly to the publication
               // $publication->setIdMed($user->getId());
    
             
            }
            else {
              
                $publication->setImagePath('');
               
            }
            $user = $entityManager->getRepository(User::class)->find($session->get('user')->getId());
            $publication->setMedecin($user);
            $em->persist($publication);
            $em->flush();
    
            return $this->redirectToRoute('list_publicationsMed');
        }
    
        return $this->render('publications/addPub.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/listPub', name: 'list_publications')]
public function listPub(PublicationsRepository $repo): Response
{
    $list = $repo->findAll(); 
  //  $medecin = $this->getUser();
    return $this->render('publications/listPub.html.twig', [
        'publications' => $list ,
      //  'medecin' => $medecin,
    ]);
}
#[Route('/listPubMed', name: 'list_publicationsMed')]
public function listPubMed(PublicationsRepository $repo): Response
{
    $list = $repo->findAll(); 
  //  $medecin = $this->getUser();
    return $this->render('publications/listPubMed.html.twig', [
        'publications' => $list ,
      //  'medecin' => $medecin,
    ]);
}

#[Route('/publications/delete/{id}', name: 'pub_deleteMed')]
public function deletePubMed(Request $request, $id,SessionInterface $session,EntityManagerInterface $entityManager, ManagerRegistry $manager, PublicationsRepository $publicationsRepository): Response
{
    $em = $manager->getManager();
    $publication = $publicationsRepository->find($id);

    // Remove associated comments
    foreach ($publication->getCommentaires() as $commentaire) {
        $em->remove($commentaire);
    }
    $user = $entityManager->getRepository(User::class)->find($session->get('user')->getId());
    if ($publication->getMedecin()->getId()==$user->getId()){
        // Remove the publication
        $em->remove($publication);
        $em->flush();
    } else {
        return new JsonResponse(['error' => 'Unauthorized'], 500);
    }
    return $this->redirectToRoute('list_publicationsMed');
}

#[Route('/updatePubForm/{id}', name: 'pub_updateMed')]
public function updatePubMed(Request $req,SessionInterface $session,EntityManagerInterface $entityManager, ManagerRegistry $manager, $id, PublicationsRepository $repo): Response
{
    $em = $manager->getManager();
    $publications = $repo->find($id);
    $user = $entityManager->getRepository(User::class)->find($session->get('user')->getId());
    if ($publications->getMedecin()->getId()==$user->getId()){
        $form = $this->createForm(AjoutPubType::class, $publications);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérez le téléchargement de l'image ici
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile instanceof UploadedFile) {
                // Générez un nom de fichier unique
                $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();

                // Déplacez le fichier dans le répertoire où vous souhaitez stocker les images
                $imageFile->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );

                // Mettez à jour le chemin de l'image dans votre entité
                $publications->setImagePath($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('list_publicationsMed');
        }
    } else {
        return new JsonResponse(['error' => 'Unauthorized'], 500);
    }
    return $this->render('publications/addPub.html.twig', ['form' => $form->createView()]);
}


#[Route('/like-toggle/{id}', name: 'like_toggle', methods: ['POST'])]
public function likeToggle(Request $request, Publications $publication): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();

    // Decode the JSON data from the request body
    $data = json_decode($request->getContent(), true);
    
    // Check if the user liked or unliked the publication
    $isLiked = $data['isLiked'];

    // For now, use a fixed Medecin ID (replace this with your actual logic when you implement user authentication)
    $medecinId = 1; // Assuming Medecin ID 1 is fixed

    // Update the nbjaime in the database
    $nbjaime = $publication->getNbjaime();

    if ($isLiked) {
        $nbjaime++;
    } else {
        $nbjaime--;
    }

    $publication->setNbjaime($nbjaime);
    
    // Add logic to track likes by Medecin ID
    // Example: $publication->addLikedByMedecin($medecinId);

    $entityManager->flush();

    // Return the updated nbjaime as JSON response
    return new JsonResponse(['nbjaime' => $publication->getNbjaime()]);
}



#[Route('/listPubAdmin', name: 'list_publicationsAdmin')]
public function listPubAdmin(PublicationsRepository $repo): Response
{
    $list = $repo->findAll(); 
  //  $medecin = $this->getUser();
    return $this->render('publications/listPubAdmin.html.twig', [
        'publications' => $list ,
      //  'medecin' => $medecin,
    ]);
}


#[Route('/publications/deleteAdmin/{id}', name: 'pub_deleteAdmin')]
public function deletePub(Request $request, $id, ManagerRegistry $manager, PublicationsRepository $publicationsRepository): Response
{
    $em = $manager->getManager();
    $publication = $publicationsRepository->find($id);

    // Remove associated comments
    foreach ($publication->getCommentaires() as $commentaire) {
        $em->remove($commentaire);
    }

    // Remove the publication
    $em->remove($publication);
    $em->flush();

    return $this->redirectToRoute('list_publicationsAdmin');
}

#[Route('/updatePubFormAdmin/{id}', name: 'pub_updateAdmin')]
public function updatePub(Request $req, ManagerRegistry $manager, $id, PublicationsRepository $repo): Response
{
    $em = $manager->getManager();
    $publications = $repo->find($id);
    $formAdmin = $this->createForm(AjoutPubTypeAdmin::class, $publications);
    $formAdmin->handleRequest($req);

    if ($formAdmin->isSubmitted() && $formAdmin->isValid()) {
        // Gérez le téléchargement de l'image ici
        $imageFile = $formAdmin->get('imageFile')->getData();

        if ($imageFile instanceof UploadedFile) {
            // Générez un nom de fichier unique
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();

            // Déplacez le fichier dans le répertoire où vous souhaitez stocker les images
            $imageFile->move(
                $this->getParameter('upload_directory'),
                $newFilename
            );

            // Mettez à jour le chemin de l'image dans votre entité
            $publications->setImagePath($newFilename);
        }

        $em->flush();

        return $this->redirectToRoute('list_publicationsAdmin');
    }

    return $this->render('publications/editPubAdmin.html.twig', ['formAdmin' => $formAdmin->createView()]);
}

#[Route('/publications/details/{id}', name: 'show_pub')]
public function show(PublicationsRepository $publicationsRepository, $id): Response
{
    $publication = $publicationsRepository->find($id);

    if (!$publication) {
        // Handle the case where the publication is not found, for example, redirect or show an error page.
        throw $this->createNotFoundException('Publication not found');
    }

    return $this->render('publications/showPub.html.twig', [
        'publications' => $publication,
    ]);
}
#[Route('/publicationsMed/details/{id}', name: 'show_pub_med')]

public function showMed(PublicationsRepository $publicationsRepository, $id): Response
{
    $publication = $publicationsRepository->find($id);

    if (!$publication) {
        // Handle the case where the publication is not found, for example, redirect or show an error page.
        throw $this->createNotFoundException('Publication not found');
    }

    return $this->render('publications/showPubMed.html.twig', [
        'publications' => $publication,
    ]);
}

#[Route('/publicationsAdmin/details/{id}', name: 'show_pub_admin')]

public function showAdmin(PublicationsRepository $publicationsRepository, $id): Response
{
    $publication = $publicationsRepository->find($id);

    if (!$publication) {
        // Handle the case where the publication is not found, for example, redirect or show an error page.
        throw $this->createNotFoundException('Publication not found');
    }

    return $this->render('publications/showPubAdmin.html.twig', [
        'publications' => $publication,
    ]);
}


}

































