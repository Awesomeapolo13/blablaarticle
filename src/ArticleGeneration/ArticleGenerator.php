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
    private $strategy;

    /**
     * @var object
     */
    private $articleDTO;

    /**
     * @param object $articleDTO - объект с данными для генерации статьи
     * @return ArticleGenerator
     */
    public function setArticleDTO(object $articleDTO): ArticleGenerator
    {
        $this->articleDTO = $articleDTO;

        return $this;
    }

    /**
     * @param ArticleGenerationInterface $strategy
     * @return ArticleGenerator
     */
    public function setGenerationStrategy(ArticleGenerationInterface $strategy): ArticleGenerator
    {
        $this->strategy = $strategy;

        return $this;
    }


    public function generateArticle()
    {
        if (empty($this->strategy) || empty($this->articleDTO)) {
            throw new \Exception('Для генерации статьи необходимо указать стратегию и данные для генерации');
        }

        return $this->strategy->generate($this->articleDTO);
    }
}