<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\CsvImportType;
use App\Form\ProfileModifierType;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Service\CsvImporter;
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



    #[Route('/import-csv', name: 'import_csv')]
    public function loadCsvFile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $csvFileForm = $this->createForm(CsvImportType::class);

        $csvFileForm->handleRequest($request);

        if ($csvFileForm->isSubmitted() && $csvFileForm->isValid()){
            $formData = $csvFileForm->getData();

            /** @var UploadedFile $csvFile */
            $csvFile = $formData['csv'];

            //ouvre le fichier temporaire... on ne le sauvegarde pas sur le serveur, on ne fait qu'en récupérer les données
            $handle = fopen($csvFile->getRealPath(), 'r');
            //on récupère les lignes une par une
            $i = -1;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {


                $i++;
                //on skip la première ligne qui contient normalement les titres de colonnes
                if ($i === 0) continue;

                //on crée un user pour cette ligne, avec des valeurs par défaut d'abord
                $user = new User();
                $user->setRoles(["ROLE_USER"]);

                //on utilise les valeurs du csv
                $user->setEmail($data[0]);

                //mot de passe par défaut
                $plainPassword = "password";

                //mais si un mot de passe est renseigné, on l'utilise
                if (!empty($data[1])){
                    $plainPassword = $data[1];
                }

                //on le hash
                $hash = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hash);

                $user->setFirstname($data[2]);
                $user->setLastname($data[3]);
                $user->setPhone($data[4]);

                //on affecte l'école choisie dans le form pour ce csv
                $user->setCampus($formData['campus']);

                //on sauvegarde chaque user...
                $entityManager->persist($user);

            }

            //mais on ne flush qu'une fois
            $entityManager->flush();

            $this->addFlash('success', $i . " participants ajoutés !");
            //recharge la page pour éviter la resoumission du csv sur un f5 par exemple
            return $this->redirectToRoute('user_import_csv');
        }

        return $this->render('admin/import_csv.html.twig', [
            'csvFileForm' => $csvFileForm->createView()
        ]);
    }



    #[Route('/telecharger-un-modele-de-csv', name: 'admin_user_download_csv_model')]
    public function downloadCsvFileModel(Request $request)
    {
        $titles = ["email", "mot de passe", "prénom", "nom", "téléphone"];
        $response = new Response(implode(", ", $titles));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="modele-participants.csv"');

        return $response;
    }
}