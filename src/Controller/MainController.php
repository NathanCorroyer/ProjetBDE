<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home', methods: ['GET', 'POST'])]
    public function home(ActivityRepository $activityRepository): Response
    {
        $activities = $activityRepository->findAll();
        return $this->render('main/home.html.twig', ['activities' => $activities]);

    }

    #[Route('/sorties', name: 'app_main_event', methods: ['GET', 'POST'])]
    public function sortie()
    {
        return $this->render('main/event.html.twig');
    }

}