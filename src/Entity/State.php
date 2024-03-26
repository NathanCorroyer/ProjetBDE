<?php

namespace App\Entity;

enum State : string
{
    case Finished = 'Terminée';
    case Cancelled = 'Annulée';
    case Archived = 'Archivée';
    case Creation = 'En Création';
    case Closed = 'Clôturée';
    case Open = 'Ouverte';
    case Ongoing = 'En Cours';

}
