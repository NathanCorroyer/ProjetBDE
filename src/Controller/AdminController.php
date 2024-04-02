<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController {
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
    $this->entityManager = $entityManager;
    }

    #[Route('/admin/users', name: 'users')]
    public function listUsers(): Response
    {
        $listUsers = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/list_user.html.twig', [
            'users' => $listUsers,
        ]);
    }

}