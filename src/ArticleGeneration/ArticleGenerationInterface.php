<?php

namespace App\ArticleGeneration;

interface ArticleGenerationInterface
{
    /**
     * Генерирует статью
     *
     * @return mixed
     */
    public function generate();
}