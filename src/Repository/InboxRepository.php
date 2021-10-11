<?php

namespace App\Repository;

use App\Entity\Inbox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Inbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inbox[]    findAll()
 * @method Inbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InboxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inbox::class);
    }

    // /**
    //  * @return Inbox[] Returns an array of Inbox objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Inbox
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
