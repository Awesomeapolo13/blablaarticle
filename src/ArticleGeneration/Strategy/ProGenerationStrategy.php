<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Article;

/**
 * Генерация статьи для пользователя с подпиской PRO
 */
class ProGenerationStrategy extends BaseStrategy
{

    /**
     * @inheritDoc
     */
    public function generate(Article $article): string
    {
        // TODO: Implement generate() method.
    }

    /**
     * Возвращает модули участвующие в процессе генерации статьи
     * По умолчанию возвращает дефолтные модули. Может быть переопределен в других стратегиях
     *
     * @param Article $article
     * @return array
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
