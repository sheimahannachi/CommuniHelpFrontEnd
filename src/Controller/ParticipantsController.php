<?php

namespace App\Controller;
use App\Entity\Test;
use App\Form\ParticipantsType;
use App\Repository\ParticipantsRepository;
use App\Entity\Participants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Dompdf\Dompdf;
use Dompdf\Options;

class ParticipantsController extends AbstractController
{
    #[Route('/participants', name: 'app_participants')]
    public function index(): Response
    {
        return $this->render('participants/index.html.twig', [
            'controller_name' => 'ParticipantsController',
        ]);
    }
    
    #[Route('/participants/add/{id}', name: 'app_add_participant')]
public function addParticipant(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $participant = new Participants();

    // Récupération de l'événement en fonction de l'ID fourni dans l'URL
    $test = $entityManager->getRepository(Test::class)->find($id);

    if (!$test) {
        throw $this->createNotFoundException('Événement non trouvé pour cet ID');
    }

    // Création du formulaire pour les participants avec l'événement pré-rempli
    $form = $this->createForm(ParticipantsType::class, $participant, [
        'test' => $test, // Transmettez l'événement au formulaire
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $participant->setTest($test); // Associez l'entité Test à Participants

        // Persistez et flush l'entité Participants
        $entityManager->persist($participant);
        $entityManager->flush();

        // Redirection après l'ajout du participant
        return $this->redirectToRoute('app_findU'); // Redirigez vers la page souhaitée
    }

    // Affichage du formulaire pour ajouter un participant avec des erreurs éventuelles
    return $this->render('participants/add.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/participants/generate-pdf', name: 'generate_pdf')]
public function generatePdf(Request $request): Response
{
    // Récupérer les données du formulaire POST
    $formData = [
        'Nom' => $request->request->get('nom'),
        'Numéro' => $request->request->get('num'),
        'Email' => $request->request->get('mail'),
        // Ajoutez d'autres champs du formulaire si nécessaire
    ];

    // Générer le contenu HTML du PDF avec les données du formulaire
    $htmlContent = $this->renderView('participants/pdf.html.twig', [
        'formData' => $formData,
    ]);

    // Créer une instance Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);

    // Charger le contenu HTML dans Dompdf
    $dompdf->loadHtml($htmlContent);

    // Rendre le PDF
    $dompdf->render();

    // Générer une réponse PDF
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');

    // Téléchargement du fichier PDF
    $response->headers->set('Content-Disposition', 'attachment; filename="donnees_formulaire.pdf"');

    return $response;
}

}