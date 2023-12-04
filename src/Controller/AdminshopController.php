<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminshopController extends AbstractController
{
    #[Route('/adminshop', name: 'app_adminshop')]
    public function index(): Response
    {
        return $this->render('adminshop/index.html.twig', [
            'controller_name' => 'AdminshopController',
        ]);
    }
}
