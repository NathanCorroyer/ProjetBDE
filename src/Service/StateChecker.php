<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\State;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use function Sodium\add;

class StateChecker
{

    public function checkState(Activity $activity, EntityManagerInterface $entityManager): void
    {
        $startingTime = $activity->getStartingDateTime();



        $nbUsers = $activity->getUsers()->count();
        $nbMaxUsers = $activity->getMaxInscription();
        $inscriptionLimitTime = $activity->getInscriptionLimitDate();
        $currentState = $activity->getState();
        $unchangeableStates = [State::Creation, State::Archived, State::Cancelled];


        //Je check si l'état de mon activité est 'En création' ou 'Archivée'.
        // Si c'est le cas, je ne veux pas les changer donc je ne vais rien faire
        if(!in_array($currentState, $unchangeableStates)){

            if($startingTime <= new \DateTime()){
                //Si la date/heure de sortie est passée alors je passe l'état à 'En cours'
                $activity -> setState(\App\Entity\State::Ongoing);
                //Calcul de la différence de temps entre le DateTime actuel et notre startingTime
                $diff = (new \DateTime())->diff($startingTime);

                $duration = $activity->getDuration();
                //On passe cette durée en seconde (en effet sinon on a un dateTimeInterface et duration est une date
                //qui démarre au 01/01/1970, ce qui pose problème pour les traitements ensuite
                $durationInSeconds = $duration->getTimestamp();

                //On passe les valeurs de diff et duration en secondes afin de pouvoir les comparer ensuite
                $diffInSeconds =
                    $diff->y * 365 * 24 * 60 * 60 + // years
                    $diff->m * 30 * 24 * 60 * 60 + // months (approximate)
                    $diff->d * 24 * 60 * 60 +       // days
                    $diff->h * 60 * 60 +            // hours
                    $diff->i * 60 +                 // minutes
                    $diff->s;                       // seconds


                if($durationInSeconds <= $diffInSeconds){
                    /*
                     * Si ça fonctionne bien, ici je teste si la différence entre l'heure et la date actuelle
                     * est supérieure à la durée de l'activité, si c'est le cas je passe l'état en finished
                    */
                    $activity->setState(State::Finished);

                    //Maintenant, on veut tester si l'activité est terminée depuis plus d'un mois
                    // On prend le startingTime comme référence, auquel on va ensuite ajouter la durée
                    $endTime = new DateTime($startingTime->format('Y-m-d H:i:s'));

                    $durationSeconds = abs($durationInSeconds); // Ensure positive value for seconds

                    $hours = floor($durationSeconds / 3600);
                    $minutes = floor(($durationSeconds % 3600) / 60);
                    $seconds = $durationSeconds % 60;

                    // Format the duration as ISO 8601 duration
                    $durationIso = sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds);

                    /*
                     * Le DateInterval accepte un paramètre string qui spécifie la durée en utilisant le format de durée
                     * ISO 8601 --> P[YYYY]-[MM]-[DD]T[HH]:[MM]:[SS]
                     * P indique que la chaine est une periode de temps
                     * T sépare la date de l'heure
                     * Ici {$durationInSeconds} spécifie le nombre de secondes
                     * S indique qu'on traite des secondes
                     */
                    $durationInterval = new DateInterval($durationIso);

                    //Enfin, on ajoute cette intervalle à notre startingTime pour obtenir notre DateTime de fin d'activité
                    $endTime->add($durationInterval);
                    //On rajoute encore un mois à la date!
                    $endTime->modify('+1 month');
                    if($endTime <= new \DateTime()){
                        //Si la date de fin d'activité est passée depuis au moins un mois, on archive
                        $activity->setState(State::Archived);
                    }
                }
            }elseif($inscriptionLimitTime <= new \DateTime() || $nbUsers>=$nbMaxUsers){
                /*
                 * Dans ce cas, l'activité n'est pas encore commencée
                 * On regarde alors si la date limite d'inscription est passée
                 */
                $activity -> setState(State::Closed);
            }else{
                /*
                 * Si l'activité n'est ni archivée, ni en création, ni annulée
                 * Qu'en plus elle n'est pas commencée et que l'inscription n'est pas finie, on l'ouvre
                 * TODO: check si ça fait pas sauter la contrainte de nombre maximal d'inscription
                 */
                $activity -> setState(State::Open);
            }

        }


        $entityManager->persist($activity);
        $entityManager->flush();
    }
}