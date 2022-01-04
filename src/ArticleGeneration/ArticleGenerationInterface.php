<?php

namespace App\ArticleGeneration;

/**
 * Интерфейс для стратегий генерации статей
 */
interface ArticleGenerationInterface
{
    /**
     * Генерирует статью
     *
     * @return mixed
     */
    public function generate();
}