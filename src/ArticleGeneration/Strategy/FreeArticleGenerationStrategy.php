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
        // ToDO Добавить флаг демо для модулей. Вытаскивать модули демо, либо все те что принадлежат пользаку.
        //   Потом выбирать из них случайное количество в рамках полученных из формы. Либо попробовать организовать
        //   from to с помощью sql
//        $modules = $this->getModuleRepository()->findDefaultWithLimit($article->getSize());
        $modules = $this->getModuleRepository()->findModulesByUserResult($article->getClient());
        $theme = $this->getThemeFactory()->findThemeBySlug($article->getTheme());
        if (!$theme) {
            throw new Exception('Тематика не найдена', 400);
        }
        // Заполняем статью контентом
        $articleBody = $this->fillPlaceholders($modules, $article);

        // Вставка текста тематики
        if ($theme->getParagraphs()) {
            foreach ($theme->getParagraphs() as $content) {
                // Вставляем ключевое слово в текст тематики
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
