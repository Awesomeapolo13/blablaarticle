<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Service\Cache;

use App\Shared\Infrastructure\Cache\CacheService;
use Symfony\Contracts\Cache\ItemInterface;

class DashboardCacheService extends CacheService
{
    /**
     * ToDo: СДелать базовую реализацию в абстрактном сервисе кеша.
     */
    public function get(string $key, mixed $data): mixed
    {
        return $this->adapter->get(
            DashboardCacheDictionary::DASHBOARD_CACHE_PREFIX . $key,
            function (ItemInterface $item) use ($data) {
                $item->expiresAfter(DashboardCacheDictionary::DASHBOARD_CACHE_TIME);
                return $data;
            }
        );
    }
}
