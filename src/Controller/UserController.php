<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\ProfileModifierType;
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


        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'campuses' => $campuses,

        ]);
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profileById(int $id, UserRepository $userRepository): Response
    {

        $user = $userRepository->find($id);

        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }


    #[Route('/modify/{id}', name: 'modify')]
    public function modifyProfile(int $id,FileUploader $fileUploader, UserRepository $userRepository,
                                  UserPasswordHasherInterface
$userPasswordHasher, CampusRepository $campusRepository, Request $request): Response
    {
        $user = $userRepository->find($id);
        $campuses = $campusRepository->findAll();
        $form = $this->createForm(ProfileModifierType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $firstName = $form->get('firstName')->getData();
            $lastName = $form->get('lastName')->getData();
            $password = $form->get('password')->getData();
            $email = $form->get('email')->getData();
            $phone = $form->get('phone')->getData();
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('avatarFile')->getData();
            if($password != null && trim($password)!=''){
                $hashedPassword = $userPasswordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }
            if($email != null && trim($email)!=''){
                $user->setEmail($email);
            }
            if($phone != null && trim($phone)){
                $user->setPhone($phone);
            }
            if($pictureFile != null) {
                $newFilename = $fileUploader->upload($pictureFile);
                $user->setAvatar($newFilename);
            }

            if($firstName != null && trim($firstName) !=''){
                $user->setFirstName($firstName);
            }
            if($lastName != null && trim($lastName) !=''){
                $user->setLastName($lastName);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('user/modify.html.twig', [
            'user' => $user,
            'campuses' => $campuses,
            'formulaire'=>$form
        ]);
    }
}