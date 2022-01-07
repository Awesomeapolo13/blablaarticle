<?php

namespace App\ArticleGeneration;

/**
 * Интерфейс для стратегий генерации статей
 */
interface ArticleGenerationInterface
{
    /**
     * Генерирует статью из переданных данных
     *
     * @var object - объект с данными для генерации статьи
     * @return mixed
     */
    public function generate(object $articleDTO);
}