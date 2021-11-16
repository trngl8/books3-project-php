<?php

namespace App\Repository;

use App\Entity\Card;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function findLatest(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('c')
            ->leftJoin('c.rescripts', 'r')
            ->where('c.active = :active')
            ->orderBy('c.updatedAt', 'DESC')
            ->setParameter('active', true)
        ;

        return (new Paginator($qb))->paginate($page);
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }
}