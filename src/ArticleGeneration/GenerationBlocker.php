<?php

namespace App\ArticleGeneration;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class GenerationBlocker
{
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
     */
    public function isBlockBySubscription(User $user)
    {
        /**
         * 1) Достать ограничения по подписке из пользователя
         * 2) На основе ограничений подписки возвращать булев флаг
         */

        return false;
    }
}
