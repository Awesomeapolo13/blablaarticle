<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Article;
use App\Entity\Module;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Генерация статьи для пользователя с подпиской FREE
 */
class FreeGenerationStrategy extends BaseStrategy
{
    /**
     * @param Article $article
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(Article $article): string
    {
        /** @var Module[] $modules */
        // ToDO Добавить флаг демо для модулей. Вытаскивать модули демо, либо все те что принадлежат пользаку.
        //   Потом выбирать из них случайное количество в рамках полученных из формы. Либо попробовать организовать
        //   from to с помощью sql
        $modules = $this->getModules($article);
        // Заполняем статью контентом
        $articleBody = $this->fillPlaceholders($modules, $article);
        // Заполняем статью контентом тематик
        $articleBody = $this->addThemeContent($article, $articleBody);
        // Вставка продвигаемых слов
        $articleBody = $this->addPromotedWords($article, $articleBody);

        return $this->getTwig()->render('article/components/article_demo.html.twig', [
            'article' => [
                'title' => '<h2 class="card-title text-center mb-4">' . $article->getTitle() . '</h2>',
                'body' => $articleBody,
            ]
        ]);
    }
}
