<?php

namespace App\Controller;

use App\Entity\Listec;

use App\Entity\LivraisonP;
use App\Entity\ProduitsInfo;
use App\Form\LivraisonPType;
use App\Repository\LivraisonPRepository;
use App\Repository\ProduitsInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\FormInterface;
use Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/livraison/p')]
class LivraisonPController extends AbstractController
{
    #[Route('/', name: 'app_livraison_p_index', methods: ['GET', 'POST'])]
    public function index(ProduitsInfoRepository $produitsInfoRepository, Request $request): Response
    {
        $sortOrder = $request->request->get('form')['sortOrder'] ?? 'asc';
        $produits_infos = $produitsInfoRepository->findByStatutAndSortOrder('available', $sortOrder);

        $form = $this->createFormBuilder()
            ->add('trier', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => [
                    'Prix croissant' => 'asc',
                    'Prix décroissant' => 'desc',
                ],
                'data' => $sortOrder,
                'attr' => ['onchange' => 'this.form.submit()'],
            ])
            ->getForm();

        return $this->render('livraison_p/index.html.twig', [
            'produits_infos' => $produits_infos,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/visual-search-results', name: 'app_visual_search_results', methods: ['GET'])]
    public function visualSearchResults(Request $request): Response
    {
        $productName = $request->query->get('productName');

        // Fetch products based on the product name
        $entityManager = $this->getDoctrine()->getManager();

        // Assuming you have an entity called ProduitsInfo with a property nomProd
        $repository = $entityManager->getRepository(ProduitsInfo::class);

        // Use a query builder to filter products by name
        $query = $repository->createQueryBuilder('p')
            ->andWhere('LOWER(p.nomProd) LIKE LOWER(:productName)')
            ->setParameter('productName', '%' . $productName . '%')
            ->getQuery();

        // Execute the query and get the results
        $filteredProducts = $query->getResult();

        // Pass the filtered products to your template
        return $this->render('livraison_p/visual_search.html.twig', ['products' => $filteredProducts, 'productName' => $productName]);
    }



    // Dans votre contrôleur Symfony
    public function searchProduct(Request $request, ProduitsInfoRepository $produitRepository): JsonResponse
    {

        $productName = $request->query->get('productName');
        $matchedProducts = $produitRepository->searchProducts($productName); // À adapter selon votre logique de recherche

        return $this->json(['results' => $matchedProducts]);
    }


    private function searchProductsByName($productName)
    {
        // Obtenez le gestionnaire d'entités EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        // Obtenez le repository pour l'entité ProduitsInfo
        $repository = $entityManager->getRepository(ProduitsInfo::class);

        // Utilisez la méthode createQueryBuilder du repository
        $queryBuilder = $repository->createQueryBuilder('p')
            ->where('p.nomProd LIKE :productName')
            ->setParameter('productName', '%' . $productName . '%');

        // Obtenez la requête
        $query = $queryBuilder->getQuery();

        // Exécutez la requête et récupérez les résultats
        $matchedProducts = $query->getResult();

        // Déboguez les résultats de la recherche
        dump($matchedProducts);

        return $matchedProducts;
    }



    private function transcribeVoiceToText($voiceRecording)
    {
        // Envoiez l'enregistrement vocal au serveur pour la transcription
        // Dans un environnement de production, vous utiliseriez un service externe pour cela.

        // Exemple simple avec un service de transcription vocale fictif
        $transcriptionServiceUrl = 'https://example.com/transcription'; // Remplacez par l'URL réelle du service

        // Utilisez cURL pour envoyer l'enregistrement vocal au service de transcription
        $ch = curl_init($transcriptionServiceUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['voiceRecording' => $voiceRecording]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        // Gérez les erreurs de cURL
        if (curl_errno($ch)) {
            throw new \Exception('Erreur lors de la transcription vocale : ' . curl_error($ch));
        }

        curl_close($ch);

        // Retournez le texte transcrit
        return $response;
    }


















    #[Route('/new', name: 'app_achat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitId = $request->query->get('produit_id');

        // Store the produit_id in the session or a temporary storage
        $this->get('session')->set('produit_id', $produitId);

        // Redirect to the confirmation page
        return $this->redirectToRoute('app_confirm_achat', ['produit_id' => $produitId]);
    }
    /*#[Route('/confirm-achat/{produit_id}', name: 'app_confirm_achat', methods: ['GET', 'POST'])]
    public function confirmAchat(int $produit_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitsinfo = $entityManager->getRepository(ProduitsInfo::class)->find($produit_id);
    
        $livraisonP = new livraisonP();
        $form = $this->createForm(LivraisonPType::class, $livraisonP);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livraisonP);
            $entityManager->flush();
    
            $listec = new Listec();
            if ($produit_id) {
                $listec->setNomproduit($produitsinfo->getNomProd());
                $listec->setContact($livraisonP->getPhonelivr());
                $listec->setNomdest($livraisonP->getNomliv());
                $listec->setEmailc($livraisonP->getemail());
                $listec->setAdressec($livraisonP->getAdresse());
    
                $entityManager->persist($listec);
    
                // Update the status of the product
                $produitsinfo->setStatutProd("not_available");
                $entityManager->persist($produitsinfo);
                $entityManager->flush();
    
                // Créer un paiement avec Stripe
                $montant = $this->calculateMontantTotal($produitsinfo); // Utilize $produitsinfo
                $paymentIntent = $this->createPaymentIntent($montant);
    
                return $this->redirectToRoute('app_livraison_p_index');
            }
        }
    
        return $this->render('livraison_p/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/


    /*#[Route('/confirm-achat/{produit_id}', name: 'app_confirm_achat', methods: ['GET', 'POST'])]
    public function confirmAchat(int $produit_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(ProduitsInfo::class)->find($produit_id);

        $form = $this->createForm(LivraisonPType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Votre logique pour gérer la soumission du formulaire

            // Créer un paiement avec Stripe
            $montant = $this->calculateMontantTotal($produit); // À vous de définir cette méthode
            $paymentIntent = $this->createPaymentIntent($montant);

            return $this->render('livraison_p/confirmation_page.html.twig', [
                'clientSecret' => $paymentIntent->client_secret,
                'produit' => $produit,
            ]);
        }

        return $this->render('livraison_p/new.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }*/









    #[Route('/{id}', name: 'app_livraison_p_show', methods: ['GET'])]
    public function show(LivraisonP $livraisonP): Response
    {
        return $this->render('livraison_p/show.html.twig', [
            'livraison_p' => $livraisonP,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_livraison_p_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LivraisonP $livraisonP, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivraisonPType::class, $livraisonP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_p_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison_p/edit.html.twig', [
            'livraison_p' => $livraisonP,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livraison_p_delete', methods: ['POST'])]
    public function delete(Request $request, LivraisonP $livraisonP, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livraisonP->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livraisonP);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livraison_p_index', [], Response::HTTP_SEE_OTHER);
    }
    /*    #[Route('/confirm-achat/{produit_id}', name: 'app_confirm_achat', methods: ['GET', 'POST'])]
    public function confirmAchat(int $produit_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitsinfo = $entityManager->getRepository(ProduitsInfo::class)->find($produit_id);
    
        $livraisonP = new livraisonP();
        $form = $this->createForm(LivraisonPType::class, $livraisonP);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livraisonP);
            $entityManager->flush();
    
            $listec = new Listec();
            if ($produit_id) {
                $listec->setNomproduit($produitsinfo->getNomProd());
                $listec->setContact($livraisonP->getPhonelivr());
                $listec->setNomdest($livraisonP->getNomliv());
                $listec->setEmailc($livraisonP->getemail());
                $listec->setAdressec($livraisonP->getAdresse());
    
                $entityManager->persist($listec);
                

                // Update the status of the product
            $produitsinfo->setStatutProd("Not Available");
            $entityManager->persist($produitsinfo);
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_p_index');


                   }
        }
    
        return $this->render('livraison_p/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    */
    /*intouuuuuuuuuuuuuuuuuuuuuuuchaaaaaaaaaaaaaaaaaaableeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee
    #[Route('/confirm-achat/{produit_id}', name: 'app_confirm_achat', methods: ['GET', 'POST'])]
public function confirmAchat(int $produit_id, Request $request, EntityManagerInterface $entityManager): Response
{
    $produitsinfo = $entityManager->getRepository(ProduitsInfo::class)->find($produit_id);

    $livraisonP = new livraisonP();
    $form = $this->createForm(LivraisonPType::class, $livraisonP);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($livraisonP);
        $entityManager->flush();

        $listec = new Listec();
        if ($produit_id) {
            $listec->setNomproduit($produitsinfo->getNomProd());
            $listec->setContact($livraisonP->getPhonelivr());
            $listec->setNomdest($livraisonP->getNomliv());
            $listec->setEmailc($livraisonP->getemail());
            $listec->setAdressec($livraisonP->getAdresse());

            $entityManager->persist($listec);

            // Update the status of the product
            $produitsinfo->setStatutProd("not_available");
            $entityManager->persist($produitsinfo);
            $entityManager->flush();

            // Créer un paiement avec Stripe
            $montant = $this->calculateMontantTotal($produitsinfo);
            $paymentIntent = $this->createPaymentIntent($montant);

            // Vérifier si le paiement a réussi
            
                // Rediriger vers une page de confirmation
                return $this->redirectToRoute('app_livraison_p_index', ['paymentIntentId' => $paymentIntent->id]);
           
        }
    }

    return $this->render('livraison_p/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

// Ajouter une nouvelle route pour la page de confirmation
#[Route('/confirmation-page/{paymentIntentId}', name: 'app_confirmation_page', methods: ['GET'])]
public function confirmationPage(string $paymentIntentId): Response
{
    // Récupérer des informations supplémentaires sur le paiement avec $paymentIntentId si nécessaire

    return $this->render('livraison_p/confirmation_page.html.twig', [
        'paymentIntentId' => $paymentIntentId,
    ]);
}

private function calculateMontantTotal(ProduitsInfo $produit): int
{
    // Supposons que la classe ProduitsInfo a une propriété prix
    $prixProduit = $produit->getPrixProd();

    // Convertir le prix en centimes (par exemple, 10,00 € => 1000 centimes)
    return $prixProduit * 100;
}
private function createPaymentIntent(int $montant): PaymentIntent
{
    Stripe::setApiKey('sk_test_51OEf1yIE7WmosFsXFp9azlkFtgqDMHC0wO96VoXtExTd44jxxd1P765jdwhwj8F7ObkSem4CHn2pDD6Bopm7taXj001W4LVvcp');

    return PaymentIntent::create([
        'amount' => $montant,
        'currency' => 'usd',  // ou une autre devise prise en charge
    ]);
}
*/
    #[Route('/confirm-achat/{produit_id}', name: 'app_confirm_achat', methods: ['GET', 'POST'])]
    public function confirmAchat(int $produit_id, Request $request,SessionInterface $session): Response
    {    $user = $session->get('user');

        $entityManager = $this->getDoctrine()->getManager();
        $produitsinfo = $entityManager->getRepository(ProduitsInfo::class)->find($produit_id);



        if (!$produitsinfo) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $livraisonP = new LivraisonP();
        $livraisonP->setNomliv($user->getNom());
        $livraisonP->setPrenomliv($user->getPrenom());
        $livraisonP->setEmail($user->getEmail());
        $livraisonP->setPhonelivr($user->getNumTel());
        $livraisonP->setAdresse($user->getAdresse());

    
        $form = $this->createForm(LivraisonPType::class, $livraisonP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livraisonP);
            $entityManager->flush();

            $listec = new Listec();
            $listec->setNomproduit($produitsinfo->getNomProd());
            $listec->setContact($livraisonP->getPhonelivr());
            $listec->setNomdest($livraisonP->getNomliv());
            $listec->setEmailc($livraisonP->getEmail());
            $listec->setAdressec($livraisonP->getAdresse());
            //$this->sendThankYouSMS($livraisonP->getPhonelivr());
            $entityManager->persist($listec);

            $produitsinfo->setStatutProd("not_available");
            $entityManager->persist($produitsinfo);
            $entityManager->flush();

            $montant = $this->calculateMontantTotal($produitsinfo);

            try {
                $paymentMethodId = $request->request->get('paymentMethodId');
                $clientId = 'cus_P34g4GLSPfwveo';
                $paymentIntent = $this->createPaymentIntent($montant, $paymentMethodId, $clientId);

                if ($paymentIntent->status === 'succeeded') {

                    $this->addFlash('success', 'Paiement réussi');
                } else {
                    $this->addFlash('error', 'Erreur de paiement : ' . $paymentIntent->last_payment_error);
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du paiement. Veuillez réessayer.');
            }

            return $this->redirectToRoute('app_livraison_p_index');
        }

        return $this->render('livraison_p/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /*private function sendThankYouSMS(string $phoneNumber): void    hedhy tekhdm

    {
        // Your Twilio Account SID, Auth Token, and Twilio phone number
        $twilioSID = 'ACc7be2c75566de2f20818841304234b0f';
        $twilioAuthToken = '1a367cc9ecef6cc6d42254ce66b8c11e';
        $twilioPhoneNumber = '+15186767789'; // Your Twilio phone number
    
        // Create a Twilio client
        $twilio = new Client($twilioSID, $twilioAuthToken);
    
        // Send a thank-you SMS
        $twilio->messages
            ->create($phoneNumber, [
                'from' => $twilioPhoneNumber,
                'body' => 'Merci pour votre achat! Nous apprécions votre soutien.'
            ]);
    }*/



    #[Route('/confirmation-page/{paymentIntentId}', name: 'app_confirmation_page', methods: ['GET'])]
    public function confirmationPage(string $paymentIntentId): Response
    {
        return $this->render('livraison_p/confirmation_page.html.twig', [
            'paymentIntentId' => $paymentIntentId,
        ]);
    }

    private function calculateMontantTotal(ProduitsInfo $produit): int
    {
        $prixProduit = $produit->getPrixProd();
        return $prixProduit * 100;
    }

    private function createPaymentIntent(int $montant, string $paymentMethodId, string $clientId): PaymentIntent
    {
        Stripe::setApiKey('sk_test_51OEf1yIE7WmosFsXFp9azlkFtgqDMHC0wO96VoXtExTd44jxxd1P765jdwhwj8F7ObkSem4CHn2pDD6Bopm7taXj001W4LVvcp');

        return PaymentIntent::create([
            'amount' => $montant,
            'currency' => 'eur',
            'payment_method' => $paymentMethodId,
            'customer' => $clientId, // Ajoutez le client ID statique ici
        ]);
    }
}
