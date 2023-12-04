<?php

namespace App\Controller;

use App\Entity\ProduitsInfo;
use App\Form\ProduitsInfoType;
use App\Repository\ProduitsInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminproduitController extends AbstractController
{
    #[Route('/adminproduit', name: 'app_admin_produit', methods: ['GET'])]
    public function index(ProduitsInfoRepository $produitsInfoRepository): Response
    {
        return $this->render('adminproduit/index.html.twig', [
            'produits_infos' => $produitsInfoRepository->findAll(),
        ]);
    }
    #[Route('hazem/{id}/edit', name: 'adminedit', methods: ['GET', 'POST'])]
    public function editadmin(Request $request, ProduitsInfo $produitsInfo, EntityManagerInterface $entityManager, KernelInterface $kernel): Response
    {
        $form = $this->createForm(ProduitsInfoType::class, $produitsInfo);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedImage = $form['image']->getData();
    
            if ($uploadedImage instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedImage->guessExtension();
    
                try {
                    $uploadedImage->move($kernel->getProjectDir() . '/public/images', $newFilename);
                } catch (FileException $e) {
                    // Handle the file upload exception
                }
    
                $produitsInfo->setImage($newFilename);
            }
    
            $entityManager->flush();
    
            $this->addFlash('success', [
                'title' => 'Notification',
                'message' => 'L admin a modifié le produit :' . $produitsInfo->getNomProd() . ' ',
            ]);
    
            return $this->redirectToRoute('app_admin_produit', ['id' => $produitsInfo->getId()]);
        }
    
        return $this->render('adminproduit/edit.html.twig', [
            'produits_info' => $produitsInfo,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('hazem/{id}', name: 'deletehazem', methods: ['POST'])]
    public function delete(Request $request, ProduitsInfo $produitsInfo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('deletehazem' . $produitsInfo->getId(), $request->request->get('_token'))) {
            $productName = $produitsInfo->getNomProd();

            $entityManager->remove($produitsInfo);
            $entityManager->flush();

            $this->addFlash('deletehazem', [
                'title' => 'Notification',
                'message' => 'L admin a supprimé le produit : ' . $productName . '',
            ]);
        }

        return $this->redirectToRoute('app_admin_produit');
    }
}
