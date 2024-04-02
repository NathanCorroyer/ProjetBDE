<?php

namespace App\Controller;

use App\Entity\State;
use App\Form\FiltersType;
use App\Repository\ActivityRepository;
use App\Service\StateChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home', methods: ['GET', 'POST'])]
    public function home(ActivityRepository $activityRepository,
    Request $request, StateChecker $stateChecker, EntityManagerInterface $entityManager): Response
    {
        $isActivitiesNull = false;

        $filterForm = $this->createForm(FiltersType::class );
        $filterForm->handleRequest($request);
        if($filterForm->isSubmitted() && $filterForm->isValid()) {
            $activities = $activityRepository->filter($filterForm->getData());

            if($activities->count()==null){
                $isActivitiesNull = true;
            }
        }else{
            $activities = $activityRepository->findAllWithUsers();
        }

        foreach($activities as $activity){
           $stateChecker->checkState($activity, $entityManager);
        }
        return $this->render('main/home.html.twig', [
            'activities' => $activities,
            'filterForm' => $filterForm,
            'isActivitiesNull' => $isActivitiesNull
        ]);


    }

}