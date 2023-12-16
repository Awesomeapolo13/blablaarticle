<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Service\Cache;

class DashboardCacheDictionary
{
    public const DASHBOARD_CACHE_TIME = 24 * 3600;
    public const DASHBOARD_CACHE_PREFIX = 'app_personal_blocks-';
}
