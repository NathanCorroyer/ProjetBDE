<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;

class StateChecker
{

    public function checkState(Activity $activity, EntityManagerInterface $entityManager): void
    {
        $startingTime = $activity->getStartingDateTime();
        $duration = $activity->getDuration();
        $inscriptionLimitTime = $activity->getInscriptionLimitDate();

        if($startingTime <= new \DateTime()){
            $activity -> setState(\App\Entity\State::Ongoing);
            if($duration <= $startingTime->diff(new \DateTime())){
                $activity->setState(State::Finished);
            }
        }elseif($inscriptionLimitTime <= new \DateTime()){
            $activity -> setState(State::Closed);
        }

        $entityManager->persist($activity);
        $entityManager->flush();
    }
}