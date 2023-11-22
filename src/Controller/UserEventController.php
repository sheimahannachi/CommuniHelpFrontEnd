<?php

namespace App\Controller;
use App\Repository\ParticipantsRepository;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserEventController extends AbstractController
{
    #[Route('/findU', name: 'app_findU')]
    public function affiche(TestRepository $testRepository, ParticipantsRepository $participantsRepository)
    {
        $events = $testRepository->findAll();
        $participantsCounts = [];

        foreach ($events as $event) {
            $id_ev = $event->getId();
            $participantsCount = $participantsRepository->countParticipantsPerTest($id_ev);
            $participantsCounts[$id_ev] = $participantsCount;
        }

        return $this->render('user_event/affiche.html.twig', [
            'event' => $events,
            'participantsCounts' => $participantsCounts,
        ]);
    }
}
