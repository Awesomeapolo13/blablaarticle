<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Article;

/**
 * Генерация статьи для пользователя с подпиской PRO
 */
class ProGenerationStrategy extends BaseStrategy
{
    /**
     * Переопределяем метод получения модулей на получение для конкретного пользователя
     */
    protected function getModules(Article $article): array
    {
        return $this->getModuleRepository()
            ->findByUserWithLimit(
                $article->getSize(),
                $article->getClient()
            );
    }
}
