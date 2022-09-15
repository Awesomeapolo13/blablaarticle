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

    /**
     * Возвращает запрос для получения статей по пользователю
     */
    public function findArticlesForUserQuery(UserInterface $user): QueryBuilder
    {
        return $this->forUser(
            $this->getOrCreateQueryBuilder(),
            $user
        )
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
     * Возвращает количество сгенерированных пользователем статей
     * @throws NonUniqueResultException
     */
    public function getCount(UserInterface $user): int
    {
        $count = $this->forUser(
            $this->forCount(),
            $user
        )
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return array_shift($count);
    }

    /**
     * Возвращает количество сгенерированных пользователем статей за последний месяц
     * @throws NonUniqueResultException
     */
    public function getLastMonthCount(UserInterface $user): int
    {
        $count = $this->forUser(
            $this->forPeriod(
                $this->forCount(),
                new DateTime('first day of this month'),
                new DateTime()
            ),
            $user
        )
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return array_shift($count);
    }

    /**
     * Возвращает переданный, либо установленный по умолчанию конструктор запросов
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }

    /**
     * Добавляет условие по пользователю
     */
    private function forUser(QueryBuilder $qb, UserInterface $user): QueryBuilder
    {
        return $qb
            ->andWhere('a.client = :client')
            ->setParameter('client', $user)
            ;
    }

    /**
     * Добавляет условия по временным рамкам
     */
    private function forPeriod(
        QueryBuilder $qb,
        DateTime     $from,
        DateTime     $to
    ): QueryBuilder {
        return $qb
            ->andWhere('a.createdAt >= :from AND a.createdAt <= :to')
            ->setParameter('from', $from->format('c'))
            ->setParameter('to', $to->format('c'))
            ;
    }

    /**
     * Возвращает исходный запрос для получения количества
     */
    private function forCount(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('COUNT(a)');
    }
}
