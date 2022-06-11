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
                // Берем рандомный модуль
                $randModulePos = rand(0, count($articleBody) - 1);
                // Ищем все параграфы в модуле
                if (
                    preg_match_all(
                        '/(?:<p(?:.*)?>)(.+)?(?:<\/p>)/',
                        $articleBody[$randModulePos],
                        $matches
                    )
                ) {
                    // Случайный текст в параграфе
                    $targetText = $matches[1][rand(0, count($matches[1]) - 1)];
                    // Делим текст абзаца на предложения
                    $sentencesArr = explode('.', $targetText);
                    // Рандомно вставляем текст тематики
                    array_splice($sentencesArr, rand(0, count($sentencesArr) - 2), 0, $content);
                    // Обрезаем выбранный случайный текст для использования его в preg_replace
                    $targetText = mb_substr($targetText, 0, 30);
                    // Осуществляем вставку дополненного текста в модуль
                    $articleBody[$randModulePos] = preg_replace(
                        "/($targetText.*?)<\/p>/",
                        implode('. ', $sentencesArr),
                        $articleBody[$randModulePos]
                    );
                }
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
