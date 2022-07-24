<?php

namespace App\ArticleGeneration;

use App\ArticleGeneration\Strategy\DemoArticleGenerationStrategy;
use App\ArticleGeneration\Strategy\FreeGenerationStrategy;
use App\ArticleGeneration\Strategy\PlusGenerationStrategy;
use App\ArticleGeneration\Strategy\ProGenerationStrategy;
use App\Entity\Article;
use App\Entity\User;

/**
 * Класс контекста для стратегий генерации статей
 */
class ArticleGenerator
{
    /**
     * Стратегия генерации статьи
     */
    private ArticleGenerationInterface $defaultStrategy;
    /**
     * @var ArticleGenerationInterface[] - коллекция стратегий для генерации статей
     */
    private iterable $strategies;

    public function __construct(
        iterable                   $strategies,
        ArticleGenerationInterface $defaultStrategy
    )
    {
        $this->strategies = $strategies;
        $this->defaultStrategy = $defaultStrategy;
    }

    /**
     * @param Article $article - объект статьи с данными для генерации
     * @return ArticleGenerator
     */
    public function setArticle(Article $article): ArticleGenerator
    {
        $this->article = $article;

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


    public function generateArticle(Article $article): string
    {
        $strategy = $this->defineStrategy($article->getClient());
        if (empty($strategy)) {
            throw new \Exception('Для генерации статьи необходимо указать стратегию и данные для генерации');
        }

        return $strategy->generate($article);
    }

    /**
     * Определяет стратегию для генерации статей в зависимости от уровня подписки пользователя
     */
    protected function defineStrategy(?User $user): ArticleGenerationInterface
    {
        $targetStrategy = null;
        // У статей генерируемых для демонстрации нет пользователя.
        // Чтобы не получить ошибку при попытке доступа к методам класса User определяем для статей без пользователя
        // по умолчанию стратегию Demo
        if (!$user) {
            return $this->defaultStrategy;
        }
        foreach ($this->strategies as $strategy) {
            switch ($user->getSubscription()->getName()) {
                case 'PLUS' && $strategy instanceof PlusGenerationStrategy:
                case 'PRO' && $strategy instanceof ProGenerationStrategy:
                case 'FREE' && $strategy instanceof FreeGenerationStrategy:
                    return $strategy;
                default:
                    $targetStrategy = $strategy;
            }
        }

        return $targetStrategy;
    }
}
