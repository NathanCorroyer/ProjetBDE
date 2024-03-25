<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home', methods: ['GET', 'POST'])]
    public function home()
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/login', name: 'app_main_login', methods: ['GET', 'POST'])]
    public function login()
    {
        return $this->render('main/login.html.twig');
    }

    #[Route('/profile', name: 'app_main_profile', methods: ['GET', 'POST'])]
    public function profile()
    {
    return $this->render('main/profile.html.twig');
    }

}