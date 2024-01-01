<?php

declare(strict_types=1);

namespace App\Users\Domain\Factory;

use App\Entity\Article;
use App\Users\Domain\Dictionary\DashboardBlockDictionary;

class DashboardBlockFactory
{
    public static function createTotalArticleCountBlock(int $count): array
    {
        return [
            'title' => $count,
            'text' => DashboardBlockDictionary::TOTAL_ARTICLE_CREATED_TEXT,
        ];
    }

    public static function createLastMonthCountBlock(int $count): array
    {
        return [
            'title' => $count,
            'text' => DashboardBlockDictionary::TOTAL_LAST_MONTH_COUNT_TEXT,
        ];
    }

    public static function createSubscriptionBlock(string $title): array
    {
        return [
            'title' => $title,
            'text' => DashboardBlockDictionary::SUBSCRIPTION_BLOCK_TEXT,
            'hrefName' => DashboardBlockDictionary::SUBSCRIPTION_BLOCK_HREF_NAME,
            'hrefPath' => DashboardBlockDictionary::SUBSCRIPTION_ROUTE,
        ];
    }
    public static function createArticleBlock(): array
    {
        return [
            'title' => DashboardBlockDictionary::CREATE_ARTICLE_TITLE,
            'hrefName' => DashboardBlockDictionary::CREATE_ARTICLE_HREF_NAME,
            'hrefPath' => DashboardBlockDictionary::CREATE_ARTICLE_ROUTE,
        ];
    }

    public static function createLastArticleBlock(int $id, string $title, ?string $description): array
    {
        return [
            'title' =>$title,
            'text' => $description,
            'hrefName' => DashboardBlockDictionary::LAST_ARTICLE_HREF_NAME,
            'hrefPath' => DashboardBlockDictionary::LAST_ARTICLE_ROUTE,
            'hrefParameters' => [
                'id' => $id
            ],
        ];
    }
}
