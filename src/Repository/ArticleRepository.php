<?php

namespace App\Repository;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findArticlesForUserQuery(UserInterface $user): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->where('a.client = :client')
            ->setParameter('client', $user)
            ->orderBy('a.createdAt', 'DESC')
        ;
    }

    /**
     * Возвращает количество статей сгенерированное за промежуток времени
     * @var DateTime $dateTimeFrom - промежуток времени с которого начинаем поиск (должен быть меньше чем $dateTimeTo)
     * @var DateTime $dateTimeTo - промежуток времени до которого проводим поиск (должен быть больше чем $dateTimeFrom)
     * @throws NonUniqueResultException
     */
    public function articlesCountForInterval(DateTime $dateTimeFrom, DateTime $dateTimeTo): int
    {
        $articlesCount =  $this->getOrCreateQueryBuilder()
            ->select('COUNT(a) as articlesCount')
            ->where('a.createdAt >= :dateTimeFrom')
            ->setParameter(':dateTimeFrom', $dateTimeFrom)
            ->andWhere('a.createdAt <= :dateTimeTo')
            ->setParameter(':dateTimeTo', $dateTimeTo)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $articlesCount ? $articlesCount['articlesCount'] : 0;
    }

    /**
     * Возвращает переданный, либо установленный по умолчанию конструктор запросов
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }
}
