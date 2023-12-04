<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\KernelInterface;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, KernelInterface $kernel): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
    
            if ($uploadedFile) {
                $imageDirectory = $kernel->getProjectDir() . '/public/images'; // Your image directory
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
    
                try {
                    $uploadedFile->move($imageDirectory, $newFilename);
                } catch (FileException $e) {
                    // Handle the file upload exception
                }
    
                $article->setImage($newFilename);
            }
    
            $entityManager->persist($article);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_article_index');
        }
    
        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager, KernelInterface $kernel): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
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
    
                $article->setImage($newFilename);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_article_index', ['id' => $article->getId()]);
        }
    
        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }
    
#[Route('/article/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
