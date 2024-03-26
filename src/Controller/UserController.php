<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_main_profile', methods: ['GET', 'POST'])]
    public function profile(): Response
    {
        $user = $this->getUser();

    return $this->render('main/profile.html.twig', [
        'user' => $user,


    ]);
    }

}