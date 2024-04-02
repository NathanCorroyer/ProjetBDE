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

    public function sendCancelNotificationToUser( Activity $activity ) {

        //$activityUsers = $activity->getUsers();

       // foreach ( $activityUsers as $user ) {


            $email =  new Email();
            $email->from('noreply@sortir.fr')
                ->to( 'boum@mail.fr' )
                ->subject('Activity Cancelled')
                ->text('Boum');


            $this->mailer->send($email);



       // }


    }

}