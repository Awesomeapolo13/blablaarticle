<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Article;
use App\Entity\Module;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Стратегия для подписчиков уровня FREE
 */
class FreeArticleGenerationStrategy extends BaseStrategy
{
    /**
     * @param Article $article
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(object $article): string
    {
        /** @var Module[] $modules */
        // ToDO Вытаскивает три одинаковых модуля. Надо либо сделать случайный выбор модулей, либо сделать
        //  первые три всегда для демо
        $modules = $this->getModuleRepository()->findDefaultWithLimit($article->getSize());
        $theme = $this->getThemeFactory()->findThemeBySlug($article->getTheme());
        if (!$theme) {
            throw new Exception('Тематика не найдена', 400);
        }
        // Заполняем статью контентом ToDO: Нужно передать сюда ключевые слова и их формы
        $articleBody = $this->fillPlaceholders($modules, $article);

        // Вставка текста тематики
        if ($theme->getParagraphs()) {
            foreach ($theme->getParagraphs() as $content) {
                // Вставляем ключевое слово в текст тематики ToDO Не выводит слово если там keyword без фильтра. Задать вопрос
                $content = $this->getTwig()->render('article/components/article_module.html.twig', [
                    'data' => ['keyword' => $article->getKeyWord()],
                    'module' => ['body' => $content],
                ]);
                // Вставка ключевого слова в параграфы тематик
                $articleBody = $this->getWordInserter()->paste(
                    $articleBody,
                    $content,
                    1,
                    [
                        'pattern' => 'p',
                        'explodeSeparator' => '.',
                        'implodeSeparator' => '.',
                    ]
                );
            }
        }

        // Вставка продвигаемых слов
        foreach ($article->getPromotedWords() as $promotedWord) {
            $articleBody = $this->getWordInserter()->paste(
                $articleBody,
                $promotedWord['word'],
                $promotedWord['count']
            );
        }

        return $this->getTwig()->render('article/components/article_demo.html.twig', [
            'article' => [
                'title' => '<h2 class="card-title text-center mb-4">' . $article->getTitle() . '</h2>',
                'body' => $articleBody,
            ]
        ]);
    }
}
