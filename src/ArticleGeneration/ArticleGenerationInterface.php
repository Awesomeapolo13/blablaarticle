<?php

namespace App\ArticleGeneration;

use App\Entity\Article;

/**
 * Интерфейс для стратегий генерации статей
 */
interface ArticleGenerationInterface
{
    /**
     * Генерирует тело статьи из переданных данных
     *
     * @var Article - объект с данными для генерации статьи
     * @return mixed
     */
    public function generate(Article $article): string;
}
