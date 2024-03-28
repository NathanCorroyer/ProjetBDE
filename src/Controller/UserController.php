<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user' , name : 'user_')]
class UserController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/myProfile', name: 'myProfile', methods: ['GET', 'POST'])]
    public function myProfile(CampusRepository $campusRepository, EntityManagerInterface $entityManager, Request $request, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        $campuses = $campusRepository->findAll();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('pictureFile')->getData();
            if($pictureFile) {
                $newFilename = $fileUploader->upload($pictureFile);
                $user->setAvatar($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_myProfile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'campuses' => $campuses,

        ]);
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profileById(int $id, UserRepository $userRepository): Response
    {

        $user = $userRepository->find($id);

        return $this->render('user/details.html.twig', [
            'user' => $user
        ]);
    }


    #[Route('/modify/{id}', name: 'modify')]
    public function modifyProfile(int $id, UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher, CampusRepository $campusRepository, Request $request): Response
    {
        $user = $userRepository->find($id);
        $campuses = $campusRepository->findAll();


        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $campusId = $request->request->get('campus');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Le mot de passe et la confirmation du mot de passe ne correspondent pas.');
                return $this->redirectToRoute('user_myProfile', ['id => $id']);
            }

            if ($password === null || empty($password)) {
                $this->addFlash('error', 'Veuillez renseigner un mot de passe.');
                return $this->redirectToRoute('user_myProfile', ['id' => $id]);
            }

            $hashedPassword = $userPasswordHasher->hashPassword($user, $password);

            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setPassword($hashedPassword);

            $campus = $campusRepository->find($campusId);
            $user->setCampus($campus);


            $this->entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('user_myProfile');
        }

        return $this->render('user/modify.html.twig', [
            'user' => $user,
            'campuses' => $campuses,
        ]);
    }
}