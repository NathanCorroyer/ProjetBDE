<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user' , name : 'user_')]
class UserController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/myProfile', name: 'myProfile', methods: ['GET', 'POST'])]
    public function myProfile(  CampusRepository $campusRepository): Response
    {
        $user = $this->getUser();
        $campuses = $campusRepository->findAll();

        return $this->render('user/profile.html.twig', [
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


    #[Route ( '/modify/{id}' , name : 'modify')]
    public function modifyProfile ( int $id , UserRepository $userRepository , CampusRepository $campusRepository  ) : Response {
        $user = $userRepository->find($id);
        $campuses = $campusRepository->findAll();

        return $this->render('user/modify.html.twig' , [
            'user' => $user,
            'campuses' => $campuses,
        ]);
    }

}
