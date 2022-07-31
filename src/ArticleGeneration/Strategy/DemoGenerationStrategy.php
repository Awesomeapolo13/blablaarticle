<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Article;
use App\Entity\Module;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Генерация статьи для демонстрации
 */
class DemoGenerationStrategy extends BaseStrategy
{
    /**
     * Генерирует демонстрационную статьи
     *
     * @param Article $article
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(Article $article): string
    {
        /** @var Module[] $modules */
        $modules = $this->getModules($article);
        // Формируем массив из модулей заполненных информацией по
        $articleBody = $this->fillPlaceholders($modules, $article);
        // Вставка продвигаемых слов
        $articleBody = $this->addPromotedWords($article, $articleBody);

        return $this->getTwig()->render('article/components/article_body.html.twig', [
            'article' => [
                'title' => '<h2 class="card-title text-center mb-4">' . $article->getTitle() . '</h2>',
                'body' => $articleBody,
            ]
        ]);
    }
}
