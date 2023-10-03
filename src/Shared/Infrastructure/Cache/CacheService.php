<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

abstract class CacheService implements CacheGetInterface
{
    public function __construct(
       protected readonly AdapterInterface $adapter
    ) {
    }

    abstract public function get(string $key, mixed $data): mixed;
}
