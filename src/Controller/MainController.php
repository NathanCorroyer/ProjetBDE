<?php

namespace App\Controller;

use App\Form\FiltersType;
use App\Repository\ActivityRepository;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home', methods: ['GET', 'POST'])]
    public function home(ActivityRepository $activityRepository,
    CampusRepository $campusRepository,
    Request $request): Response
    {
        $campuses = $campusRepository->findAll();
        $activities = $activityRepository->findAllWithUsers();

        $filterForm = $this->createForm(FiltersType::class);
        $filterForm->handleRequest($request);
        if($filterForm->isSubmitted()) {
            $activities = $activityRepository->filter($filterForm->getData());

        }

        return $this->render('main/home.html.twig', ['campuses' => $campuses,
            'activities' => $activities,
            'filterForm' => $filterForm]);

    }

}