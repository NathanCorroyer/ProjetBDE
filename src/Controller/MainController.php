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
    Request $request): Response
    {


        $filterForm = $this->createForm(FiltersType::class );
        $filterForm->handleRequest($request);
        if($filterForm->isSubmitted() && $filterForm->isValid()) {
            $activities = $activityRepository->filter($filterForm->getData());

        }else{
            $activities = $activityRepository->findAllWithUsers();
        }

        return $this->render('main/home.html.twig', [
            'activities' => $activities,
            'filterForm' => $filterForm]);

    }

}