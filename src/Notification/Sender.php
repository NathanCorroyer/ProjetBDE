<?php

namespace App\Notification;

use App\Entity\Activity;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Sender
{
    protected $mailer ;

    public function __construct( MailerInterface $mailer ) {
        $this->mailer = $mailer ;
    }

    public function sendCancelNotificationToUser( Activity $activity , $motifAnnulation  ) {

        $activityUsers = $activity->getUsers();
        $activityName = $activity->getName();

        foreach ( $activityUsers as $user ) {
            $email =  new Email();
            $email->from('noreply@sortir.fr')
                ->to( $user->getEmail() )
                ->subject($activityName . ' cancelled')
                ->text( $motifAnnulation );


            $this->mailer->send($email);

        }
    }

}