<?php

namespace App\Entity;

enum State : string
{
    case Finished = 'Terminee';
    case Cancelled = 'Annulee';
    case Archived = 'Archivee';
    case Creation = 'En creation';
    case Closed = 'Cloturee';
    case Open = 'Ouverte';
    case Ongoing = 'En cours';

}
