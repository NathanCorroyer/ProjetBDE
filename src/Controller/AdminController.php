<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin')]
#[IsGranted("ROLE_ADMIN")]
class AdminController extends AbstractController {

    #[Route('/home', name: 'home')]
    public function adminHome(): Response
    {
        return $this->render('admin/home.html.twig');
    }
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
    $this->entityManager = $entityManager;
    }

    #[Route('/cities', name:'cities')]
    public function listCities(): Response
    {
    $cityRepository = $this->entityManager->getRepository(City::class);
    $cities = $cityRepository->findAll();

    return $this->render('admin/list_cities.html.twig' , [
        'cities' => $cities,
        ]);
    }

    #[Route('/users', name: 'users')]
    public function listUsers(): Response
    {
        $listUsers = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/list_user.html.twig', [
            'users' => $listUsers,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($user);
        $entityManager->flush();


        return $this->redirectToRoute('app_adminusers');
    }


}