<?php

declare(strict_types=1);

namespace App\Users\Domain\Dictionary;

class DashboardBlockDictionary
{
    public const TOTAL_ARTICLE_CREATED_TEXT = 'Всего статей создано.';
    public const TOTAL_LAST_MONTH_COUNT_TEXT = 'Создано в этом месяце.';

    public const SUBSCRIPTION_BLOCK_TEXT = 'Ваш уровень подписки.';
    public const SUBSCRIPTION_BLOCK_HREF_NAME = 'Улучшить';
    public const SUBSCRIPTION_ROUTE = 'app_admin_subscription';

    public const CREATE_ARTICLE_TITLE = 'Создать статью';
    public const CREATE_ARTICLE_HREF_NAME = 'Создать';
    public const CREATE_ARTICLE_ROUTE = 'app_admin_article_create';

    public const LAST_ARTICLE_HREF_NAME = 'Подробнее';
    public const LAST_ARTICLE_ROUTE = 'app_admin_article_show';

    public const SUBSCRIPTION_EXPIRES_TEXT = 'Подписка истекает ';
    public const IS_EXPIRES_SOON_DAYS = 3;
}
