<?php

namespace App\Controller;

use App\Entity\Campus;
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

        $campus = $this->getUser()->getCampus()->getName();
        $campusString = 'Liste des activités sur le campus de '. $campus.':';
        $filterForm = $this->createForm(FiltersType::class );
        $filterForm->handleRequest($request);
        if($filterForm->isSubmitted() && $filterForm->isValid()) {
            $activities = $activityRepository->filter($filterForm->getData());
            $campus = $filterForm->get('campus')->getData();
            if($campus != null){

                $campus = $campus->getName();
                $campusString = 'Liste des activités sur le campus de '. $campus .':';

            }else{
                $campusString = 'Liste des activités de tous les campus:';
            }
            if($activities->count()==null){
                $isActivitiesNull = true;
            }
        }else{
            $activities = $activityRepository->findAllWithUsersFromUserCampus();
        }

        foreach($activities as $activity){
           $stateChecker->checkState($activity, $entityManager);
        }
        return $this->render('main/home.html.twig', [
            'activities' => $activities,
            'filterForm' => $filterForm,
            'isActivitiesNull' => $isActivitiesNull,
            'campusString' => $campusString,
            'campus' => $campus,
        ]);


    }

}