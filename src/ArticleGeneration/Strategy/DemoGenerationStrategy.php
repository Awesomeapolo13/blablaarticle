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
     * Метод формирования данных для тела статьи.
     * Переопределен для демонстрационной генерации
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function prepareArticleBody(Article $article): array
    {
        /** @var Module[] $modules */
        $modules = $this->getModules($article);
        // Формируем массив из модулей заполненных информацией по
        $articleBody = $this->fillPlaceholders($modules, $article);
        // Вставка продвигаемых слов
        $articleBody = $this->addPromotedWords($article, $articleBody);

        return $articleBody;
    }
}
