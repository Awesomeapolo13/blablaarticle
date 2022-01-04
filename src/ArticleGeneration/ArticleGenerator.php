<?php

namespace App\ArticleGeneration;

/**
 * Класс контекста для стратегий генерации статей
 */
class ArticleGenerator
{
    /**
     * Стратегия генерации статьи
     *
     * @var ArticleGenerationInterface
     */
    private $generator;

    public function __construct(ArticleGenerationInterface $generator)
    {
        $this->generator = $generator;
    }

    public function generateArticle()
    {
        return $this->generator->generate();
    }
}