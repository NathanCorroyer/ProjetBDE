<?php

namespace App\MessageHandler;

use App\Message\ArchiveActivityMessage;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ArchiveActivityMessageHandler
{
    public function __construct(  private ActivityRepository $activityRepository , private EntityManagerInterface $entityManager )
    {
    }

    public function __invoke(ArchiveActivityMessage $message)
    {
        $activities = $this->activityRepository->findAll();
        $now = new \DateTime();

        foreach ( $activities as $activity ) {
            $activityStart = $activity->getStartingDateTime();
            $oneMonthLater = $activityStart->modify('+1 month');

            if ( $now < $oneMonthLater ){
                $activity->setState(State::Archived);
                $this->entityManager->persist($activity);
                $this->entityManager->flush();
            }
        }
    }
}
