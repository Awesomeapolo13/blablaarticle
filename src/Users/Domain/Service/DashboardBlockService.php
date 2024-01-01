<?php

declare(strict_types=1);

namespace App\Users\Domain\Service;

use App\Users\Domain\Factory\DashboardBlockFactory;

class DashboardBlockService
{
    /**
     * ToDo: Сделать потом билдер для блоков.
     */
    public function getBlocks(
        int $totalArticlesCount,
        int $lastMonthArticlesCount,
        string $subscriptionName,
        ?int $lastArticleId,
        ?string $lastArticleTitle,
        ?string $lastArticleDescription
    ): array {
        $blocks = [
            DashboardBlockFactory::createTotalArticleCountBlock($totalArticlesCount),
            DashboardBlockFactory::createLastMonthCountBlock($lastMonthArticlesCount),
            DashboardBlockFactory::createSubscriptionBlock($subscriptionName),
            DashboardBlockFactory::createArticleBlock(),
        ];
        // Если у пользователя есть статья (последняя созданная) то выводим блок с ней
        if (isset($lastArticleId) && isset($lastArticleTitle)) {
            $blocks[] = DashboardBlockFactory::createLastArticleBlock(
                $lastArticleId,
                $lastArticleTitle,
                $lastArticleDescription
            );
        }

        return $blocks;
    }
}
