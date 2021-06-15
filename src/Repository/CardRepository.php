<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Invite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    private CONST DQL = 'SELECT c FROM App\Entity\Card c left join c.rescripts r';

    private CONST MAX_RESULT = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invite::class);
    }

    public function createQueryWithPaginator(int $firstResult = 0, int $maxResult = self::MAX_RESULT) : Query
    {
        return $this->getEntityManager()->createQuery(self::DQL)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResult);

    }
}