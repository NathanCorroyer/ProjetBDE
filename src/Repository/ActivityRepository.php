<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\State;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\PseudoTypes\IntegerValue;
use Symfony\Bundle\SecurityBundle\Security;
use function Symfony\Component\String\u;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    private $security;
    private $user;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        $this->security = $security;
        parent::__construct($registry, Activity::class);
        /** @var User $user */
        $this->user = $this->security->getUser();
    }

    public function findAllWithUsers(){
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->leftJoin('a.users', 'u')
            ->addSelect('u');
        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);

        return $paginator;
    }

    public function filter($filtres){
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.campus','c')
            ->leftJoin('a.users', 'u')
            ->addSelect('u', 'c');


        foreach ($filtres as $key => $value){

            if(strcmp($key, 'campus') == 0 && $value != null){
                $queryBuilder->andWhere('c.id = :value')
                ->setParameter('value', $value->getId());
            }elseif(strcmp($key,'searchbar') == 0 && $value != null && $value != ''){
                $queryBuilder->andWhere('a.name like :textValue')
                ->setParameter('textValue', '%' . $value . '%');
            }elseif(strcmp($key, 'status_filter') == 0){
                foreach($value as $status){
                    switch ($status){

                        case 'planner' :
                            $queryBuilder
                                ->andWhere('a.planner = :planner')
                                ->setParameter('planner', $this->user->getId());

                            break;
                        case 'followed' :
                            $queryBuilder -> andWhere(':user MEMBER OF a.users')
                                ->setParameter('user',$this->user);
                            break;
                        case 'nonfollowed' :
                            $queryBuilder -> andWhere(':user NOT MEMBER OF a.users')
                                ->setParameter('user',$this->user);
                            break;
                        case 'finished' :
                            $queryBuilder -> andWhere('a.state = :state')
                                -> setParameter('state', State::Finished);
                            break;
                    }
                }
            }
        }

        $query = $queryBuilder->getQuery();
        return new Paginator($query);
    }
}
