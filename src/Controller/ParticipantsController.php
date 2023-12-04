<?php

namespace App\Controller;
use App\Entity\Test;
use App\Form\ParticipantsType;
use App\Repository\ParticipantsRepository;
use App\Entity\Participants;
use App\Service\UtilisateurService;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
public function addParticipant(Request $request, EntityManagerInterface $entityManager, int $id,SessionInterface $session): Response
{
    $participant = new Participants();

    // Récupération de l'événement en fonction de l'ID fourni dans l'URL
    $test = $entityManager->getRepository(Test::class)->find($id);
    $user = $session->get('user');

    if ($user) {
        $participant->setNom($user->getNom()); // Set firstNameEtud
        $participant->setNum($user->getNumTel()); // Set lastNameEtud
        $participant->setMail($user->getEmail()); // Set phoneEtud
        }






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
        'test'=> $test,
    ]);
}
#[Route('/participants/generate-pdf', name: 'generate_pdf')]
    public function generatePdf(Request $request): Response
    {
        // Récupérer les données soumises par le formulaire
        $nom = $request->get('nom');
        $num = $request->get('num');
        $mail = $request->get('mail');
        // Ajoutez d'autres champs si nécessaire

        // Générer le contenu HTML du PDF avec les données récupérées
        $htmlContent = $this->renderView('participants/pdf.html.twig', [
            'nom' => $nom,
            'num' => $num,
            'mail' => $mail,
            // Ajoutez d'autres champs si nécessaire
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
    #[Route('/delete/{id}', name: 'app_deletepar', methods: ['POST'])]
    public function deletepar(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $part = $entityManager->getRepository(Participants::class)->find($id);

        if (!$part) {
            throw $this->createNotFoundException('Aucun participant trouvé pour l\'ID : ' . $id);
        }

        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $entityManager->remove($part); // Utilisez $part ici au lieu de $$part
            $entityManager->flush();
            
            // Ajoutez un message flash pour indiquer que le participant a été supprimé avec succès
            $this->addFlash('success', 'Le participant a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_findpar', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/sendemailMembers', name: 'sendEmailToAllMembers')]
    public function sendEmail(Request $request, UtilisateurService $serviceMail): Response
    {
        $serviceMail->sendEmail('Test1 ',  "waaaaaa", 'nadhir.tebini@esprit.tn');

        return $this->redirectToRoute('app_connect');
    }
 
    #[Route('/send-emails-periodically', name: 'send_emails_periodically')]
public function sendEmailsPeriodically(MailerInterface $mailer, UtilisateurService $serviceMail): Response
{
    $participants = $this->getDoctrine()->getRepository(Participants::class)->findAll();

    foreach ($participants as $participant) {
        for ($i = 0; $i < 3; $i++) {
            $serviceMail->sendEmail('Remember', 'Nous tenons à vous rappeler que événement  se déroulera dans un avenir proche Votre présence est importante pour le succès de cet événement', $participant->getMail());
            sleep(1); 
        }

        sleep(5); // Pause de 5 secondes entre chaque série de 3 e-mails
    }

    return $this->redirectToRoute('app_findpar');
}
    
}