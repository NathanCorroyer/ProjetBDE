<?php

namespace App\Controller\Api;

use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api' , name: 'api_')]
class ApiController extends AbstractController
{

    #[Route('/listeUsers' , name: 'listeUsers' , methods: ['GET'])]
    public function listeUser ( UserRepository $userRepository ) : JsonResponse {

        $users = $userRepository->findAll();
        return $this->json($users , Response::HTTP_OK , [] , ['groups' => 'liste_users']);
    }

    #[Route('/listeActivites' , name : 'listeActivites' , methods: ['GET'])]
    public function listeActivites ( ActivityRepository $activityRepository ) : JsonResponse {

        $activites = $activityRepository->findAll();
        return $this->json( $activites , Response::HTTP_OK , [] , ['groups' => 'liste_activites']);

    }

}
