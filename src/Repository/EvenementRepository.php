<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }
    public function Eventaujourdhui($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateDebut = :val')
            ->setParameter('val', $value)

            ->getQuery()
            ->getResult()
            ;
    }
    public function SearchEvent($criteria)
    {

        if($criteria['selon']=='Nom'){
            return $this->createQueryBuilder('e')
                ->andwhere('e.nomEvenement like :nom')
                ->setParameter('nom', $criteria['rech'].'%')
                ->getQuery()
                ->getResult()
                ;
        }
        if($criteria['selon']=='Description'){
            return $this->createQueryBuilder('e')
                ->andwhere('e.description like :description')
                ->setParameter('description', $criteria['rech'].'%')
                ->getQuery()
                ->getResult()
                ;
        }
        if($criteria['selon']=='Prix'){
            return $this->createQueryBuilder('e')
                ->andwhere('e.prix=:prix')
                ->setParameter('prix', $criteria['rech'])
                ->getQuery()
                ->getResult()
                ;
        }

    }

    public function TrierEvent($criteria)
    {

        if($criteria['selon']=='Nom'){
            return $this->createQueryBuilder('e')
                ->addOrderBy('e.nomEvenement', 'DESC')
                ->getQuery()
                ->getResult()
                ;
        }
        if($criteria['selon']=='Description'){
            return $this->createQueryBuilder('e')
                ->addOrderBy('e.description', 'DESC')
                ->getQuery()
                ->getResult()
                ;
        }
        if($criteria['selon']=='Prix'){
            return $this->createQueryBuilder('e')
                ->addOrderBy('e.prix', 'DESC')
                ->getQuery()
                ->getResult()
                ;
        }

    }
    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
