<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\State;
use App\Entity\User;
use App\Form\CreateActivityType;
use App\Message\ArchiveActivityMessage;
use App\Notification\Sender;
use App\Repository\ActivityRepository;

use App\Repository\PlaceRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity', name : 'activity_')]
class ActivityController extends AbstractController
{

    public function __construct()
    {
    }

    #[Route('/register/{id}', name : 'register')]
    public function addUsersToActivity(int $id, ActivityRepository $activityRepository, EntityManagerInterface $em) : Response
    {
        $activity = $activityRepository->find($id);
        $maxInscription = $activity->getMaxInscription();
        $nbUsers = $activity->getUsers()->count();
        $state = $activity ->getState();

        if($maxInscription-1 >= $nbUsers && $state == State::Open){
            /** @var User $user */
            $user = $this->getUser();
            $activity->addUser($user);

            if($nbUsers>=$maxInscription){
                $activity->setState(State::Closed);
            }

            $em->persist($activity);
            $em->flush();
            $this->addFlash('succes' , 'Vous venez de vous inscrire à cette activité !');
            return $this->render('activity/details.html.twig', [
                'activity' => $activity
            ]);
        }else{
            switch ($state){
                case State::Creation :
                    $this->addFlash('echec', 'Impossible de s\'inscrire à cette activité, elle n\'est pas encore ouverte à l\"inscription !');
                    break;
                case State::Cancelled :
                    $this->addFlash('echec', 'Impossible de s\'inscrire à cette activité, elle a été annulée !');
                    break;
                case State::Finished :
                    $this->addFlash('echec', 'Impossible de s\'inscrire à cette activité, elle est terminée !');
                    break;
                case State::Ongoing :
                    $this->addFlash('echec', 'Impossible de s\'inscrire à cette activité, elle est en cours !');
                    break;
                case State::Archived :
                    $this->addFlash('echec', 'Impossible de s\'inscrire à cette activité, elle est finie depuis très longtemps :) !');
                    break;
                case State::Closed :
                    $this->addFlash('echec', 'Impossible de s\'inscrire à cette activité, elle est fermée à l\'inscription !');
                    break;
            }

            return $this->render('activity/details.html.twig', [
                'activity'=> $activity
            ]);
        }



    }

    #[Route( '/desist/{id}' , name: 'desist')]
    public function removeUserFromActivity ( int $id , ActivityRepository $activityRepository , EntityManagerInterface $entityManager ) : Response {
        $activity = $activityRepository->find($id);

        $user = $this->getUser();
        $activity->removeUser($user);


        if ( $activity->getUsers()->count() < $activity->getMaxInscription() ){
            $activity->setState(State::Open);
        }

        $entityManager->persist($activity);
        $entityManager->flush();

        $this->addFlash('succes' , 'Vous venez de vous désister de cette activité !');

        return $this->render('activity/details.html.twig' , [
            'activity' => $activity
        ]);
    }

    #[Route( '/details/{id}' , name : "details")]
    public function details ( int $id , ActivityRepository $activityRepository ) : Response {

        $activity = $activityRepository->find($id);


        return $this->render('activity/details.html.twig' , [
            'activity' => $activity
        ]
        );
    }

    #[Route( '/create' , name : "create")]
    public function create( Request $request, EntityManagerInterface $entityManager) : Response
    {
        $user = $this->getUser();
        
        $campus= $user->getCampus();
        $activity = new Activity();
        $activity->setCampus($campus)
            ->setPlanner($user);
        $activityForm = $this->createForm(CreateActivityType::class, $activity, ['attr' =>
            ['id'=>'formulaireActivity']]);

        $activityForm->handleRequest($request);


        if ($activityForm->isSubmitted() && $activityForm->isValid()){
            $duration = $activityForm->get('durationInMinutes')->getData();
            if($duration){
                $hours = floor($duration/60);
                $minutes = $duration % 60;

                $time = new \DateTime();
                $time->setTime($hours, $minutes);

                $activity->setDuration($time);
            }
            switch ($activityForm->getClickedButton()->getName()){
                case 'save' :
                    $activity->setState(State::Creation);
                break;
                case 'publish' :
                    $activity->setState(State::Open);
                    break;
                case 'return' :
                    return $this->redirectToRoute('app_main_home');
                default: $activity->setState(State::Open);
            }

            $entityManager->persist($activity);
            $entityManager->flush();

            $this->addFlash('success', 'Idea successfully added');
            return $this->redirectToRoute('activity_details',['id' => $activity->getId()]);
        }

        return $this->render('activity/create.html.twig', ['activityForm' => $activityForm->createView(),
            "user"=>$user,
            "campus"=>$campus
        ]);
    }

    #[Route('/cancel/{id}' , name : 'cancel' , methods: "GET")]
    public function cancelActivity ( int $id , ActivityRepository $activityRepository ) {

        $activity = $activityRepository->find($id);

        return $this->render('activity/cancel.html.twig' , [
            'activity' => $activity ]);
    }



     #[Route("/places/{cityId}", name : "places_by_city", methods : "GET")]
    public function getPlacesByCity($cityId, PlaceRepository $placeRepository): Response
    {
        $places = $placeRepository->findBy(['city' => $cityId]);
        $options = '';
        if($places) {

            foreach ($places as $place) {
                $options .= '<option value="' . $place->getId() . '">' . $place->getName() . '</option>';
            }
        }
        return new Response($options);
    }

    #[Route('/edit/{id}', name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Activity $activity): Response
    {
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est autorisé à modifier cette activité
        // Cela peut dépendre de la relation entre l'utilisateur et l'activité
        // Par exemple, vérifiez si l'utilisateur est l'organisateur de l'activité

        $activityForm = $this->createForm(CreateActivityType::class, $activity);
        $activityForm->handleRequest($request);

        if ($activityForm->isSubmitted() && $activityForm->isValid()) {
            $duration = $activityForm->get('durationInMinutes')->getData();
            if ($duration) {
                $hours = floor($duration / 60);
                $minutes = $duration % 60;

                $time = new \DateTime();
                $time->setTime($hours, $minutes);

                $activity->setDuration($time);
            }

            switch ($activityForm->getClickedButton()->getName()) {
                case 'save':
                    $activity->setState(State::Creation);
                    break;
                case 'publish':
                    $activity->setState(State::Open);
                    break;
                case 'return':
                    return $this->redirectToRoute('app_main_home');
                default:
                    $activity->setState(State::Open);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Activity successfully updated');
            return $this->redirectToRoute('activity_details', ['id' => $activity->getId()]);
        }

        return $this->render('activity/edit.html.twig', [
            'activityForm' => $activityForm->createView(), 'id' => $activity->getId()
        ]);
    }

    #[Route('/archive' , name : 'archive' )]
    public function archiveById( MessageBusInterface $messageBus ) : Response {

        $messageBus->dispatch( new ArchiveActivityMessage());

        return $this->redirectToRoute('app_main_home');
    }



    #[Route("/delete/{id}", name:"delete")]

    public function supprimer( ActivityRepository $activityRepository, $id , Sender $sender, EntityManagerInterface $entityManager , MailerInterface $mailer ): Response
    {

        // Récupérer l'activité à supprimer en fonction de son ID
        $activity = $activityRepository->find($id);

        // Vérifier si l'activité existe
        if (!$activity) {
            throw $this->createNotFoundException('L\'activité n\'existe pas.');
        }

        //Envoi d'un mail aux personnes enregistrées sur l'activité
        $email =  new Email();
        $email->from('noreply@sortir.fr')
            ->to( 'boum@mail.fr' )
            ->subject('Activity Cancelled')
            ->text('Boum');


       $mailer->send($email);


        // Supprimer l'activité
        //$entityManager->remove($activity);
        //$entityManager->flush();


        // Répondre avec un code de succès
        $this->addFlash('succes', 'Mail envoyé');

        return $this->redirectToRoute('app_main_home');
    }


}
