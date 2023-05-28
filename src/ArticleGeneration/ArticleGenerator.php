<?php

namespace App\ArticleGeneration;

use App\ArticleGeneration\Strategy\FreeGenerationStrategy;
use App\ArticleGeneration\Strategy\PlusGenerationStrategy;
use App\ArticleGeneration\Strategy\ProGenerationStrategy;
use App\Entity\Article;
use App\Users\Domain\Entity\User;
use Exception;

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
     * @throws Exception
     */
    public function generateArticle(Article $article): string
    {
        $strategy = $this->defineStrategy($article->getClient());
        if (empty($strategy)) {
            throw new Exception('Для генерации статьи необходимо указать стратегию и данные для генерации');
        }

        return $strategy->generate($article);
    }

    /**
     * Определяет стратегию для генерации статей в зависимости от уровня подписки пользователя
     */
    protected function defineStrategy(?User $user): ArticleGenerationInterface
    {
        $userSubscription = $user?->getSubscription()->getName();

        foreach ($this->strategies as $strategy) {
            if (
                $userSubscription === 'PRO' && $strategy instanceof ProGenerationStrategy ||
                $userSubscription === 'PLUS' && $strategy instanceof PlusGenerationStrategy ||
                $userSubscription === 'FREE' && $strategy instanceof FreeGenerationStrategy
            ) {
                return $strategy;
            }
        }
        // У статей генерируемых для демонстрации нет пользователя.
        return $this->defaultStrategy;
    }
}
