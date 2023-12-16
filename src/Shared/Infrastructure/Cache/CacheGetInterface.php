<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Cache;

interface CacheGetInterface
{
    public function get(string $key, mixed $data): mixed;
}
