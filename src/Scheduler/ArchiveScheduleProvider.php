<?php

namespace App\Scheduler ;

use App\Message\ArchiveActivityMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
class ArchiveScheduleProvider implements ScheduleProviderInterface{

    public function getSchedule(): Schedule
    {
        return ( new Schedule())->add(
            RecurringMessage::every('5 sec' , new ArchiveActivityMessage())
        );
    }
}