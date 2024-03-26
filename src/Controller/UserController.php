<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_main_profile', methods: ['GET', 'POST'])]
    public function profile(): Response
    {
        $user = $this->getUser();
        $campuses = $this->entityManager->getRepository(Campus::class)->findAll();

        return $this->render('main/profile.html.twig', [
            'user' => $user,
            'campuses' => $campuses,
        ]);
    }
}
