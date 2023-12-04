<?php

namespace App\Controller;
use App\Entity\Article;
use Stripe\Stripe;
use App\Entity\Donationforms;
use App\Entity\History;
use App\Form\DonationformsType;
use App\Repository\ArticleRepository;
use App\Repository\DonationformsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/donationforms')]
class DonationformsController extends AbstractController
{
    #[Route('/', name: 'app_donationforms_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll(); // Fetch articles using the ArticleRepository

        return $this->render('donationforms/index.html.twig', [
            'articles' => $articles,
        ]);
    }

  /*  #[Route('/new', name: 'app_donationforms_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $donationform = new Donationforms();
        $form = $this->createForm(DonationformsType::class, $donationform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($donationform);
            $entityManager->flush();

            return $this->redirectToRoute('app_donationforms_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('donationforms/new.html.twig', [
            'donationform' => $donationform,
            'form' => $form,
        ]);
    }*/
  /* #[Route('/new', name: 'app_donationforms_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $articleId = $request->query->get('article_id');
    
        // Store the article_id in the session or a temporary storage
        $this->get('session')->set('article_id', $articleId);
    
        return $this->redirectToRoute('app_donationforms_index');
    }



    #[Route('/confirm-donation/{article_id}', name: 'app_confirm_donation', methods: ['GET'])]
    public function confirmDonation(int $article_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch the related article entity based on $article_id
        $article = $entityManager->getRepository(Article::class)->find($article_id);
    
        // Fetch the donation details from the request
        $donationData = $request->request->all();
    
        // Create a new History entry
        $history = new History();
        $history->setArticleId($article_id);
        $history->setArticleVille($article->getVille());
        $history->setDescription($article->getDescription());
        $history->setContact($article->getContact());
        $history->setImage($article->getImage());

        // Assuming the donationform entity has these fields



        $entityManager->persist($history);
        $entityManager->flush();
    
        return $this->renderForm('donationforms/new.html.twig', [
        ]);    }

*/



#[Route('/new', name: 'app_donationforms_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $articleId = $request->query->get('article_id');

    // Store the article_id in the session or a temporary storage
    $this->get('session')->set('article_id', $articleId);

    // Redirect to the donation form
    return $this->redirectToRoute('app_confirm_donation', ['article_id' => $articleId]);
}

#[Route('/confirm-donation/{article_id}', name: 'app_confirm_donation', methods: ['GET', 'POST'])]
public function confirmDonation(int $article_id, Request $request, EntityManagerInterface $entityManager,ArticleRepository $articleRepository,SessionInterface $session): Response
{
    $user = $session->get('user');
    $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];
    $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
    Stripe::setApiKey($stripeSecretKey);

    $article = $entityManager->getRepository(Article::class)->find($article_id);

    $donationform = new Donationforms();
    $donationform->setNom($user->getNom());
    $donationform->setPrenom($user->getPrenom());
    $donationform->setEmail($user->getEmail());

    $form = $this->createForm(DonationformsType::class, $donationform);
    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $donationform->getMontant() * 100,
                'currency' => 'eur',
            ]);

            $entityManager->persist($donationform);

            $history = new History();
            $history->setArticle($article);
            $history->setDonationform($donationform);
            $history->setArticleVille($article->getVille());
            $history->setDescription($article->getDescription());
            $history->setContact($article->getContact());
            $history->setImage($article->getImage());
            $history->setMontant($donationform->getMontant());
            $history->setEmail($donationform->getEmail());
            $history->setNom($donationform->getNom());
            $history->setPrenom($donationform->getPrenom());
            $history->setDonationVille($donationform->getVille());
            $history->setCarteBancaire($donationform->getCarteBancaire());

            $entityManager->persist($history);
            $entityManager->flush();
            $articles = $articleRepository->findAll();

            return $this->render('donationforms/index.html.twig', [
                
                'form' => $form->createView(),
                'stripe_public_key' => $stripePublicKey,
                'client_secret' => $paymentIntent->client_secret,
                'articles' => $articles,
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            $this->addFlash('error', 'Card payment failed: ' . $e->getMessage());
            return $this->redirectToRoute('app_donationforms_index');
        }
    }
    $yourVariableValue = 'pk_test_51OCKLhCxG8BbbtlfukrZQ8ihHEGtJ1MmsZpvHJUj9hD66icKGZemjV9T6bebA8H2k3WPPBgmTo5Xt6FIPwrIwLyE00y29S0Dda';
   // ...
   return $this->render('donationforms/new.html.twig', [
    'form' => $form->createView(),
    'stripe_public_key' => $stripePublicKey,
    'pk_test_51OCKLhCxG8BbbtlfukrZQ8ihHEGtJ1MmsZpvHJUj9hD66icKGZemjV9T6bebA8H2k3WPPBgmTo5Xt6FIPwrIwLyE00y29S0Dda' => $stripePublicKey,
    'sk_test_51OCKLhCxG8BbbtlfhknuEejqO5wg1yAPQjFc100a3UnuPbNIsnYJ47xJOy4rIzzZ1zyVVC6D41OfCl141EUOC8Zb00qQAPNb8f' => $yourVariableValue,
]);

}

    





    #[Route('/{id}', name: 'app_donationforms_show', methods: ['GET'])]
    public function show(Donationforms $donationform): Response
    {
        return $this->render('donationforms/show.html.twig', [
            'donationform' => $donationform,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_donationforms_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Donationforms $donationform, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DonationformsType::class, $donationform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_donationforms_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('donationforms/edit.html.twig', [
            'donationform' => $donationform,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_donationforms_delete', methods: ['POST'])]
    public function delete(Request $request, Donationforms $donationform, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donationform->getId(), $request->request->get('_token'))) {
            $entityManager->remove($donationform);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_donationforms_index', [], Response::HTTP_SEE_OTHER);
    }
}
