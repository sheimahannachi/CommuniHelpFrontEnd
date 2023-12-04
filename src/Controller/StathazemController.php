<?php



namespace App\Controller;

use App\Repository\ListecRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitsInfoRepository;
// StathazemController.php

use Symfony\Component\HttpFoundation\JsonResponse;

class StathazemController extends AbstractController
{
    #[Route('/stathazem/stats', name: 'app_stathazem_stats')]
    public function stats(ProduitsInfoRepository $produitsInfoRepository): JsonResponse
    {
        $produits = $produitsInfoRepository->findAll();

        // Calcul du nombre total de produits
        $totalProduits = count($produits);

        // Calcul de la moyenne des prix
        $totalPrix = array_sum(array_map(fn ($produit) => $produit->getPrixProd(), $produits));
        $moyennePrix = $totalProduits > 0 ? $totalPrix / $totalProduits : 0;

        // Calcul du nombre de produits par statut
        $statuts = [];
        foreach ($produits as $produit) {
            $statut = $produit->getStatutProd();
            $statuts[$statut] = ($statuts[$statut] ?? 0) + 1;
        }

        // Calcul du prix total des produits par statut
        $prixTotalParStatut = [];
        foreach ($produits as $produit) {
            $statut = $produit->getStatutProd();
            $prixTotalParStatut[$statut] = ($prixTotalParStatut[$statut] ?? 0) + $produit->getPrixProd();
        }

        // Calcul du prix moyen des produits par statut
        $prixMoyenParStatut = [];
        foreach ($statuts as $statut => $nombre) {
            $prixMoyenParStatut[$statut] = $nombre > 0 ? $prixTotalParStatut[$statut] / $nombre : 0;
        }

        // Autres statistiques selon vos besoins

       
            return new JsonResponse([
                'totalProduits' => $totalProduits,
                'moyennePrix' => $moyennePrix,
                'statuts' => $statuts,
                'prixTotalParStatut' => $prixTotalParStatut,
                'prixMoyenParStatut' => $prixMoyenParStatut
            // Ajoutez d'autres statistiques au besoin
        ]);
    }

    #[Route('/stathazem', name: 'app_stathazem')]
    public function index(): Response
    {
        return $this->render('stathazem/index.html.twig');
    }
}