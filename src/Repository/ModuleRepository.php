<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Репозиторий модулей для генерации статей
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    /**
     * Ищет дефолтные модули
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    public function findAllDefaultModulesQuery(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->notDeleted($qb)
            ->andWhere('m.client IS NULL OR m.isDefault = true')
            ->orderBy('m.createdAt', 'DESC')
            ;
    }

    /**
     * Ищет модули, принадлежащие конкретному пользователю и возвращает запрос
     *
     * @param UserInterface $user - владелец модулей
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    public function findModulesByUserQuery(UserInterface $user, QueryBuilder $qb = null): QueryBuilder
    {
        return $this->notDeleted($qb)
            ->andWhere('m.client = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('m.createdAt', 'DESC')
            ;
    }

    /**
     * Ищет модули, принадлежащие конкретному пользователю и возвращает их
     * @param int $limit
     * @param UserInterface $user
     * @param QueryBuilder|null $qb
     * @return array
     */
    public function findByUserWithLimit(
        int $limit,
        UserInterface $user,
        QueryBuilder $qb = null
    ): array {
        return $this->findModulesByUserQuery($user, $qb)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Получает определенное количество дефолтных модулей
     *
     * @param int $limit
     * @param QueryBuilder|null $qb
     * @return float|int|mixed|string
     */
    public function findDefaultWithLimit(int $limit, QueryBuilder $qb = null): mixed
    {
        return $this->findAllDefaultModulesQuery($this->withLimit($limit, $qb))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Возвращает переданный, либо установленный по умолчанию конструктор запросов
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $qb): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('m');
    }

    /**
     * Возвращает QueryBuilder для не удаленных модулей
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function notDeleted(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('m.deletedAt IS NULL')
            ;
    }

    /**
     * Возвращает QueryBuilder для запросов с лимитом на количество записей в БД
     * @param int $limit
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function withLimit(int $limit, QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->setMaxResults($limit);
    }
}
