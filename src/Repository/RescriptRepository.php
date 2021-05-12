<?php

namespace App\Repository;

use App\Entity\Rescript;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rescript|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rescript|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rescript[]    findAll()
 * @method Rescript[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RescriptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rescript::class);
    }

    // /**
    //  * @return Rescript[] Returns an array of Rescript objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rescript
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
