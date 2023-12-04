<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\History;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Your logic to fetch and calculate statistics
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(History::class);

        $statisticsData = [
            'totalRecords' => $repository->count([]),
            'averageDonation' => $repository->createQueryBuilder('h')->select('AVG(h.montant)')->getQuery()->getSingleScalarResult(),
            'totalDonationAmount' => $repository->createQueryBuilder('h')->select('SUM(h.montant)')->getQuery()->getSingleScalarResult(),
            'minDonationAmount' => $repository->createQueryBuilder('h')->select('MIN(h.montant)')->getQuery()->getSingleScalarResult(),
            'maxDonationAmount' => $repository->createQueryBuilder('h')->select('MAX(h.montant)')->getQuery()->getSingleScalarResult(),
        ];

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'statisticsData' => $statisticsData,
        ]);
    }

    #[Route('/admin/statistics', name: 'app_admin_statistics')]
    public function statistics(): JsonResponse
    {
        // Your logic to fetch and calculate statistics
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(History::class);

        $statisticsData = [
            'totalRecords' => $repository->count([]),
            'averageDonation' => $repository->createQueryBuilder('h')->select('AVG(h.montant)')->getQuery()->getSingleScalarResult(),
            'totalDonationAmount' => $repository->createQueryBuilder('h')->select('SUM(h.montant)')->getQuery()->getSingleScalarResult(),
            'minDonationAmount' => $repository->createQueryBuilder('h')->select('MIN(h.montant)')->getQuery()->getSingleScalarResult(),
            'maxDonationAmount' => $repository->createQueryBuilder('h')->select('MAX(h.montant)')->getQuery()->getSingleScalarResult(),
        ];

        return $this->json($statisticsData);
    }
    #[Route('/admin/pie-chart', name: 'app_admin_pie_chart')]
    public function pieChart(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(History::class);

        // Fetch the number of donations per city
        $donationsByCity = $repository->createQueryBuilder('h')
            ->select('h.donationVille AS city, COUNT(h.id) AS donationCount')
            ->groupBy('h.donationVille')
            ->getQuery()
            ->getResult();

        return $this->json($donationsByCity);
    }
    #[Route('/admin/line-chart', name: 'app_admin_line_chart')]
    public function lineChart(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(History::class);

        // Fetch the distribution of donations based on amount ranges
        $donationsDistribution = $repository->createQueryBuilder('h')
            ->select('CASE
                        WHEN h.montant <= 10 THEN \'0-10\'
                        WHEN h.montant <= 20 THEN \'11-20\'
                        WHEN h.montant <= 30 THEN \'21-30\'
                        ELSE \'30+\'
                    END AS amountRange, COUNT(h.id) AS donationCount')
            ->groupBy('amountRange')
            ->getQuery()
            ->getResult();

        return $this->json($donationsDistribution);
    }
    
}