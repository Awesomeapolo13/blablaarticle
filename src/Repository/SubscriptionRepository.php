<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий подписок
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * Возвращает переданный, либо установленный по умолчанию конструктор запросов
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $qb): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('s');
    }

    /**
     * Возвращает коллекцию подписок, отсортированную по возрастанию по цене
     *
     * @param QueryBuilder|null $qb
     * @return Subscription[]
     */
    public function findSubscriptionsOrderedByPrice(QueryBuilder $qb = null): array
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->orderBy('s.price', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
