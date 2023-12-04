<?php

namespace App\Controller;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{
    #[Route('/admin/article', name: 'app_admin_article', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator): Response
    {
        $query = $articleRepository->createQueryBuilder('a')
            ->orderBy('a.creationDate', 'DESC')
            ->getQuery();

        $articles = $paginator->paginate(
            $query,
            $this->get('request_stack')->getCurrentRequest()->query->getInt('page', 1),
            3 // items per page
        );
        $form = $this->createForm(ArticleType::class);
        $cities = $form->get('ville')->getConfig()->getOption('choices');
        return $this->render('admin_article/index.html.twig', [
            'articles' => $articles,
            'cities' => $cities,
        ]);
    }

    
    #[Route('oumayma/{id}', name: 'deleteouma', methods: ['POST'])]
public function deleteouma(Request $request, Article $article, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('deleteouma'.$article->getId(), $request->request->get('_token'))) {
        $entityManager->remove($article);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_admin_article', [], Response::HTTP_SEE_OTHER);
}
#[Route('oumayma/{id}', name: 'showw', methods: ['GET'])]
public function showw(Article $article): Response
{
    return $this->render('admin_article/show.html.twig', [
        'article' => $article,
    ]);
}
#[Route('/display-article/{id}', name: 'display_article', methods: ['GET'])]
public function displayArticle(Article $article): Response
{
    return $this->render('admin_article/show.html.twig', [
        'article' => $article,
    ]);
}









#[Route('/admin/search-dons', name: 'admin_search_dons', methods: ['POST'])]
public function searchDons(Request $request, ArticleRepository $articleRepository): JsonResponse
{
    try {
        // Récupérer les paramètres de la requête Ajax
        $ville = $request->request->get('ville');
        $joursRestants = $request->request->get('joursRestants');
        // Appeler le service ou effectuer la logique pour récupérer les appels au dons
        $dons = $articleRepository->getDonsByCriteria($ville, $joursRestants);
        dump("ahawa chfih");

        dump($dons);
        // Retourner les résultats en format JSON
        return new JsonResponse(['dons' => $this->formatDonsForJson($dons)]);    } catch (\Exception $e) {
        // Return a JSON response with an error message
        return new JsonResponse(['error' => 'An internal server error occurred.'], 500);
    }
}

private function formatDonsForJson(array $dons): array
{
    $formattedDons = [];

    foreach ($dons as $don) {
        $daysDifference = $this->calculateDaysDifference($don->getCreationDate());        $progressBar = $this->calculateProgressBar($don->getCreationDate());
        // Customize this part based on your entity structure
        $formattedDon = [
            'id' => $don->getId(),
            'ville' => $don->getVille(),
            'description' => $don->getDescription(),
            'contact' => $don->getContact(),
            'creationDate' => $don->getCreationDate()->format('Y-m-d H:i:s'), // Adjust the format as needed
            'image' => $don->getImage(),
            'daysDifference' => $daysDifference,
            'progressBar' => $progressBar,
        ];

        $formattedDons[] = $formattedDon;
    }

    return $formattedDons;
}






#[Route('/admin/calculate-jours-options', name: 'calculate_jours_options', methods: ['POST'])]
    public function calculateJoursOptions(Request $request): JsonResponse
    {
        $selectedCity = $request->request->get('ville');
        // Perform your calculations based on the selected city
        $joursOptions = $this->calculateJoursOptionsForCity($selectedCity);

        // Return the calculated options as JSON
        return $this->json(['joursOptions' => $joursOptions]);
    }

    private function calculateJoursOptionsForCity(string $city): array
    {
        // Get the current date
        $currentDate = new \DateTime();
    
        // Query the database to get articles for the selected city
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(['ville' => $city]);
    
        // Calculate joursRestants options based on the difference between creationDate and current date
        $joursOptions = [];
        foreach ($articles as $article) {
            // Calculate days remaining
            $daysRemaining = $article->getCreationDate()->diff($currentDate)->days;
    
            // Determine the range based on days remaining
            if ($daysRemaining >= 0 && $daysRemaining <= 10) {
                $joursOptions[] = '0-10';
            } elseif ($daysRemaining > 10 && $daysRemaining <= 20) {
                $joursOptions[] = '10-20';
            } elseif ($daysRemaining > 20 && $daysRemaining <= 50) {
                $joursOptions[] = '20-50';
            } elseif ($daysRemaining > 50 && $daysRemaining <= 150) {
                $joursOptions[] = '50-150';
            } elseif ($daysRemaining > 150 && $daysRemaining <= 300) {
                $joursOptions[] = '150-300';
            } elseif ($daysRemaining > 300 && $daysRemaining <= 400) {
                $joursOptions[] = '300-400';
            } elseif ($daysRemaining > 400 && $daysRemaining <= 600) {
                $joursOptions[] = '400-600';
            }
            // Add more conditions as needed for different ranges
        }
    
        // Deduplicate the array to ensure unique values
        $joursOptions = array_unique($joursOptions);
    
        return $joursOptions;
    }

    private function calculateDaysDifference($creationDate)
    {
        $now = new DateTime();
        $daysDifference = $creationDate->diff($now)->days;

        return $daysDifference;
    }

    // Function to calculate the progress bar based on days remaining
    private function calculateProgressBar($creationDate): ?float
    {
        $totalDays = 100;
        $now = new DateTime();
        $daysRemaining = $creationDate->diff($now)->days;

        if ($totalDays > 0) {
            $percentage = (($totalDays - $daysRemaining) / $totalDays) * 100;
            return $percentage;
        }

        return null;
    }
}