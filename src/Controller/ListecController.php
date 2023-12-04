<?php

namespace App\Controller;

use App\Entity\Listec;
use App\Entity\ProduitsInfo;
use App\Form\ListecType;
use App\Repository\ListecRepository;
use App\Repository\ProduitsInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/listec')]
class ListecController extends AbstractController
{
    #[Route('/', name: 'app_listec_index', methods: ['GET'])]
    public function index(ListecRepository $listecRepository): Response
    {
        return $this->render('listec/index.html.twig', [
            'listecs' => $listecRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_listec_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $listec = new Listec();
        $form = $this->createForm(ListecType::class, $listec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($listec);
            $entityManager->flush();

            return $this->redirectToRoute('app_listec_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('listec/new.html.twig', [
            'listec' => $listec,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_listec_show', methods: ['GET'])]
    public function show(Listec $listec): Response
    {
        return $this->render('listec/show.html.twig', [
            'listec' => $listec,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_listec_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Listec $listec, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ListecType::class, $listec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_listec_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('listec/edit.html.twig', [
            'listec' => $listec,
            'form' => $form,
        ]);
    }
    
/*
    #[Route('/cancel-order/{id}', name: 'cancelOrder', methods: ['POST'])]
   // Example debugging in your cancelOrder method
public function cancelOrder(
    Request $request,
    Listec $listec,
    EntityManagerInterface $entityManager,
    ProduitsInfoRepository $produitsInfoRepository,
    ListecRepository $listecRepository
): Response {
    // Check the entire request data
    dump($request->request->all());

    // Check relevant data from $listec
    dump($listec->getId(), $listec->getNomproduit());

    // Check if 'form' key exists in the request
   
    // Get the product name from the form data
    $productName = $request->request->get('form')['Nomproduit']; // Correct case

    // Check if $listec->getProduitsInfo() is not null
    if ($produitsInfo = $listec->getProduitsInfo()) {
        // Use the custom repository method
        $produit = $produitsInfoRepository->findByProductNameAndCancelOrder($productName, $entityManager, $listecRepository);

        // Check if the product is found and cancellation is successful
        
    } else {
        // Handle the case where $listec->getProduitsInfo() is null
        return $this->redirectToRoute('error_page');
    }
}
*/
#[Route('/cancel-order/{id}', name: 'cancelOrder')]
    public function cancelOrder(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $listec = $entityManager->getRepository(Listec::class)->find($id);

        if (!$listec) {
            throw $this->createNotFoundException('Listec not found');
        }

        // Assuming the nomproduit attribute uniquely identifies a produit
        $nomProduit = $listec->getNomproduit();
        
        // Find the ProduitsInfo entity by nomProduit
        $produitInfo = $entityManager->getRepository(ProduitsInfo::class)->findOneBy(['nomProd' => $nomProduit]);

        if ($produitInfo) {
            // Update the status to "available" (assuming statusProd is the attribute for status)
            $produitInfo->setStatutProd('available');
            
            // Persist the changes
            $entityManager->persist($produitInfo);
            $entityManager->flush();
            // Remove the Listec entry
    $entityManager->remove($listec);
    $entityManager->flush();
        }

        // Your existing code for handling the cancellation...

        return $this->redirectToRoute('app_listec_index', [], Response::HTTP_SEE_OTHER);    }
}
