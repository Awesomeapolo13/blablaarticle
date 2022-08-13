<?php

namespace App\ArticleGeneration;

use App\Entity\Subscription;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Реализует разные типы блокировки генерации статей
 */
class GenerationBlocker
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Задает ограничение на создание статьи для демонстрации
     */
    public function blockDemo(Response $response, int $articleId): Response
    {
        $cookie = new Cookie('articleId', $articleId, 2147483647, '/');
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * Проверяет можно ли сгенерировать еще статьи в соответствии с подпиской пользователя
     * @return bool - вернут true, если генерация заблокирована, false - если нет
     * @throws NonUniqueResultException
     */
    public function isBlockBySubscription(Subscription $subscription): bool
    {
        /**
         * 1) Достать ограничения по подписке из пользователя
         * 2) Получить количество статей указанное сгенерированное за последние $blockTime минут
         * 3) Сравнить количество с $blockCount
         * 4) Если меньше то вернуть false, если больше то true
         */
        $blockCount = $subscription->getBlockCount();
        $blockInterval = $subscription->getBlockTime();
        // Если ограничения не заданы подпиской, то блокировать не нужно
        if (!$blockCount || !$blockInterval) {
            return false;
        }

        $dateTimeTo = new DateTime();
        $dateTimeFrom = (clone $dateTimeTo)
            ->modify('-' . $subscription->getBlockTime() . ' minutes');

        return $this->articleRepository->articlesCountForInterval($dateTimeFrom, $dateTimeTo) >= $blockCount;
    }
}