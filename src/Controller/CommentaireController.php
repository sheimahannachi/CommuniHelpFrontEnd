<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commentaire;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Publications;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;


class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    #[Route('/commentaire/add/{id}', name: 'add_comm')]
    public function addComm(ManagerRegistry $manager,EntityManagerInterface $entityManager, Request $request, $id,SessionInterface $session): Response
    {
        $em = $manager->getManager();
    
        // Load the Publication entity
        $publication = $em->getRepository(Publications::class)->find($id);
    
        $commentaire = new Commentaire();
        $user = $entityManager->getRepository(User::class)->find($session->get('user')->getId());
        $commentaire->setMedecin($user);
        $commentaire->setPublications($publication);
        $commentaire->setContenucommentaire($request->request->get('commentText'));
    
        $em->persist($commentaire);
        $em->flush();
    
        // Return the new comment as JSON response
        $response = new JsonResponse([
            'id' => $commentaire->getIdComm(),
            'contenucommentaire' => $commentaire->getContenucommentaire(),
            // Add other fields if needed
        ]);
    
        return $response;
    }
    #[Route('/api/comments/{id}', name: 'api_comments')]
    public function getComments($id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Assuming you have an entity named 'Publications' and a OneToMany association to 'Commentaire'
        $publications = $entityManager->getRepository(Publications::class)->find($id);

        if (!$publications) {
            // Handle case when the publication is not found
            return new JsonResponse(['error' => 'Publication not found'], 404);
        }

        // Fetch comments associated with the given publication and fixed Medecin ID
        $comments = $entityManager->getRepository(Commentaire::class)
            ->findBy(['publications' => $publications]);

        // You can format the comments array based on your needs
        $formattedComments = [];
        foreach ($comments as $comment) {
            $formattedComments[] = [
                'id' => $comment->getIdComm(),
                'content' => $comment->getContenucommentaire(),
                // Add other fields as needed
            ];
        }

        return new JsonResponse($formattedComments);
    }

    #[Route('/commentaire/delete/{id}', name: 'delete_comment')]
public function deleteComment(Request $request, SessionInterface $session, CommentaireRepository $commentaireRepository, EntityManagerInterface $entityManager, $id): JsonResponse
{
    $comment = $commentaireRepository->find($id);

    if (!$comment) {
        return new JsonResponse(['error' => 'Comment not found'], 404);
    }
    $user = $entityManager->getRepository(User::class)->find($session->get('user')->getId());
    if ($comment->getMedecin()->getId()==$user->getId()){
        $entityManager->remove($comment);
        $entityManager->flush();
    } else {
        return new JsonResponse(['error' => 'Unauthorized'], 500);
    }

    return new JsonResponse(['message' => 'Comment deleted successfully']);
}

#[Route('/commentaire/edit/{id}', name: 'edit_comment')]
public function editComment(Request $request,SessionInterface $session, CommentaireRepository $commentaireRepository, EntityManagerInterface $entityManager, $id): JsonResponse
{
    $comment = $commentaireRepository->find($id);

    if (!$comment) {
        return new JsonResponse(['error' => 'Comment not found'], 404);
    }

    $newText = $request->request->get('newText');
    $user = $entityManager->getRepository(User::class)->find($session->get('user')->getId());
    if ($comment->getMedecin()->getId()==$user->getId()){
        $comment->setContenucommentaire($newText);
        $entityManager->flush();
    } else {
        return new JsonResponse(['error' => 'Unauthorized'], 500);
    }

    return new JsonResponse(['message' => 'Comment edited successfully']);
}





}