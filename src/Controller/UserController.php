<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    #[Route('/myProfile', name: 'myProfile', methods: ['GET', 'POST'])]
    public function myProfile(): Response
    {
        $user = $this->getUser();
        $campuses = $this->entityManager->getRepository(Campus::class)->findAll();

        return $this->render('main/profile.html.twig', [
            'user' => $user,
            'campuses' => $campuses,
        ]);
    }

    #[Route( '/profile/{id}' , name : 'profile')]
    public function profileById ( int $id , UserRepository $userRepository ) : Response {

        $user = $userRepository->find($id);

        return $this->render('user/details.html.twig' , [
            'user' => $user
        ]);
    }

}
