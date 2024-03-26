<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActivityController extends AbstractController
{

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    #[Route('/activity/register/{id}', name : '')]
    public function addUsersToActivity(int $id, ActivityRepository $activityRepository) : Response
    {
        $activity = $activityRepository->find($id);

        $violations = $this->validator->validate($activity);

        if (count($violations) > 0) {
            throw new \Exception('Impossible, il y a déjà trop d\'utilisateurs inscrits');
        }

        /** @var User $user */
        $user = $this->getUser();
        $activity->addUser($user);

        return $this->render('/activity/details/'.$id, [
            'message' => 'Vous avez bien été inscrit à cette activité'
        ]);
    }
    #[Route('/activity', name: 'app_activity')]
    public function index(): Response
    {
        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}
