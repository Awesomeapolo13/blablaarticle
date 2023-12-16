<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Users\Domain\Entity\ApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }
}
