<?php

namespace App\Controller;

use App\Entity\State;
use App\Entity\User;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/activity', name : 'activity_')]
class ActivityController extends AbstractController
{

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    #[Route('/register/{id}', name : 'register')]
    public function addUsersToActivity(int $id, ActivityRepository $activityRepository, EntityManagerInterface $em) : Response
    {
        $activity = $activityRepository->find($id);

        $violations = $this->validator->validate($activity);

        if (count($violations) > 0) {
            throw new \Exception('Impossible, il y a déjà trop d\'utilisateurs inscrits');
        }

        /** @var User $user */
        $user = $this->getUser();
        $activity->addUser($user);



        if($activity->getUsers()->count()>=$activity->getMaxInscription()){
            $activity->setState(State::Closed);
        }

        $em->persist($activity);
        $em->flush();
        return $this->render('/activity/details/'.$id, [
            'message' => 'Vous avez bien été inscrit à cette activité'
        ]);
    }

    #[Route( '/details/{id}' , name : "details")]
    public function details ( int $id , ActivityRepository $activityRepository ) : Response {

        $activity = $activityRepository->find($id);


        return $this->render('activity/details.html.twig' , [
            'activity' => $activity
        ]
        );
    }

}
