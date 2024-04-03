<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Form\CampusFormType;
use App\Form\CityFormType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/cities/{id}/delete', name: 'city_delete')]
    public function deleteCity(City $city, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($city);
        $entityManager->flush();

        return $this->redirectToRoute('app_admincities');
    }
    #[Route('/cities/{id}/modify', name: 'city_modify')]
    public function modifyCity(Request $request, int $id, EntityManagerInterface $entityManager, CityRepository $cityRepository):
    Response
    {
        $city = $cityRepository->find($id);
        $city->setName($request->get('newCityName'));

        $entityManager->persist($city);
        $entityManager->flush();
        // Rendre à nouveau la liste des villes après la modification
        $cities = $cityRepository->findAll();

        return $this->render('admin/list_cities.html.twig', [
            'cities' => $cities,
        ]);
    }

    #[Route('/cities/add', name: 'app_add_city')]
    public function addCity(Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = new City();
        $form = $this->createForm(CityFormType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le code postal depuis le formulaire et le définir sur l'objet City
            $city->setZipCode($form->get('zipcode')->getData());

            // Persister et enregistrer la ville
            $entityManager->persist($city);
            $entityManager->flush();

            // Rediriger vers la liste des villes après l'ajout
            return $this->redirectToRoute('app_admincities');
        }

        // Afficher le formulaire d'ajout de ville
        return $this->render('admin/add_city.html.twig', [
            'form' => $form->createView(),
        ]);
    }
        #[Route('/campuses', name:'campuses')]
        public function listCampuses(): Response
    {
        $campusRepository = $this->entityManager->getRepository(Campus::class);
        $campuses = $campusRepository->findAll();

        return $this->render('admin/list_campuses.twig', [
            'campuses' => $campuses,
        ]);



    }

    #[Route('/campuses/add', name: 'app_add_campus')]
    public function addCampus(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('app_admincampuses');
        }

        return $this->render('admin/add_campus.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/campuses/{id}/delete', name: 'app_delete_campus')]
    public function deleteCampus(Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();

        return $this->redirectToRoute('app_admincampuses');
    }

    #[Route('/campuses/{id}/modify', name: 'app_modify_campus')]
    public function modifyCampus(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admincampuses');
        }

        return $this->render('admin/modify_campus.html.twig', [
            'form' => $form->createView(),
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