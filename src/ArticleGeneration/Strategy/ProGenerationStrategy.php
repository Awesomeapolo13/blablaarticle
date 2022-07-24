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
}
