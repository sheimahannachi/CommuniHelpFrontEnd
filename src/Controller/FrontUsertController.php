<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontUsertController extends AbstractController
{
    #[Route('/front/usert', name: 'app_front_usert')]
    public function index(): Response
    {
        return $this->render('front_usert/baseuser.html.twig', [
            'controller_name' => 'FrontUsertController',
        ]);
    }
}
