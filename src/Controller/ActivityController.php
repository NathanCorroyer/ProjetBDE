<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\State;
use App\Entity\User;
use App\Form\CreateActivityType;
use App\Repository\ActivityRepository;
use App\Repository\CampusRepository;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/activity', name : 'activity_')]
class ActivityController extends AbstractController
{

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    #[Route('/register/{id}', name : 'register')]
    public function addUsersToActivity(int $id, ActivityRepository $activityRepository, EntityManagerInterface $em) : Response
    {
        $activity = $activityRepository->find($id);

        $violations = $this->validator->validate($activity);

        if (count($violations) > 0) {
            throw new \Exception('Impossible, il y a déjà trop d\'utilisateurs inscrits');
        }

        /** @var User $user */
        $user = $this->getUser();
        $activity->addUser($user);



        if($activity->getUsers()->count()>=$activity->getMaxInscription()){
            $activity->setState(State::Closed);
        }

        $em->persist($activity);
        $em->flush();
        $this->addFlash('succes' , 'Vous venez de vous inscrire à cette activité !');
        return $this->render('activity/details.html.twig', [
            'activity' => $activity
        ]);
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

    #[Route( '/create' , name : "create", methods: ["GET", "POST"])]
    public function create( Request $request, EntityManagerInterface $entityManager) : Response
    {
        $user = $this->getUser();
        
        $campus= $user->getCampus();
        $activity = new Activity();
        $activity->setCampus($campus)
            ->setPlanner($user);
        $activityForm = $this->createForm(CreateActivityType::class, $activity);

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

}
