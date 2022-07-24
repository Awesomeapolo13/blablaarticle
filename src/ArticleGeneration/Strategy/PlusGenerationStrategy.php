<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Article;

/**
 * Генерация статьи для пользователя с подпиской PLUS
 */
class PlusGenerationStrategy extends BaseStrategy
{

    /**
     * @inheritDoc
     */
    public function generate(Article $article): string
    {
        // TODO: Implement generate() method.
    }
}
