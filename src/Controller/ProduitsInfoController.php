<?php

namespace App\Controller;

use App\Entity\ProduitsInfo;
use App\Form\ProduitsInfoType;
use App\Repository\ProduitsInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/produits/info')]

class ProduitsInfoController extends AbstractController
{
  
    #[Route('/', name: 'app_produits_info_index', methods: ['GET'])]
    public function index(ProduitsInfoRepository $produitsInfoRepository): Response
    {
        return $this->render('produits_info/index.html.twig', [
            'produits_infos' => $produitsInfoRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_produits_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, KernelInterface $kernel): Response
    {
        $produitsInfo = new ProduitsInfo();
        $form = $this->createForm(ProduitsInfoType::class, $produitsInfo);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
    
            if ($uploadedFile) {
                $imageDirectory = $kernel->getProjectDir() . '/public/images'; // Your image directory
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
    
                try {
                    $uploadedFile->move($imageDirectory, $newFilename);
                } catch (FileException $e) {
                    // Handle the file upload exception
                }
    
                $produitsInfo->setImage($newFilename);
            }
    
            $entityManager->persist($produitsInfo);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_produits_info_index');
        }
    
        return $this->render('produits_info/new.html.twig', [
            'produits_info' => $produitsInfo,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id}', name: 'app_produits_info_show', methods: ['GET'])]
    public function show(ProduitsInfo $produitsInfo): Response
    {
        return $this->render('produits_info/show.html.twig', [
            'produits_info' => $produitsInfo,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_produits_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProduitsInfo $produitsInfo, EntityManagerInterface $entityManager, KernelInterface $kernel): Response
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
    
            // Redirect to the detail view after editing
            return $this->redirectToRoute('app_produits_info_index', ['id' => $produitsInfo->getId()]);
        }
    
        return $this->render('produits_info/edit.html.twig', [
            'produits_info' => $produitsInfo,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id}', name: 'app_produits_info_delete', methods: ['POST'])]
    public function delete(Request $request, ProduitsInfo $produitsInfo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitsInfo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produitsInfo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produits_info_index', [], Response::HTTP_SEE_OTHER);
    }

}
