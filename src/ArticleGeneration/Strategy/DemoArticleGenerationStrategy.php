<?php

namespace App\ArticleGeneration\Strategy;

use App\Entity\Module;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Генерация статьи для демонстрации
 *
 * ToDo: доработать генерацию, после того как будет реализован функционал тематик, модулей и прочее
 */
class DemoArticleGenerationStrategy extends BaseStrategy
{
    /**
     * Количество продвигаемых слов для демонстрационной генерации статьи
     */
    private const PROMO_WORD_COUNT = 1;

    /**
     * Количество модулей для демонстрационной генерации статьи
     */
    private const DEMO_MODULES_COUNT = 3;

    /**
     * Генерирует демонстрационную статьи
     *
     * @param object $articleDTO
     * @return string - вложенный массив для сохранения с данными новой статьи
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(object $articleDTO): string
    {
        /** @var Module[] $modules */
        // ToDO Вытаскивает три одинаковых модуля. Надо либо сделать случайный выбор модулей, либо сделать
        //  первые три всегда для демо
        $modules = $this->getModuleRepository()->findDefaultWithLimit(self::DEMO_MODULES_COUNT);

        $articleBody = $this->getWordInserter()->paste(
            $this->fillPlaceholders($modules),
            $articleDTO->promotedWord,
            self::PROMO_WORD_COUNT
        );

        return $this->getTwig()->render('article/components/article_demo.html.twig', [
            'article' => [
                'title' => '<h2 class="card-title text-center mb-4">' . $articleDTO->title . '</h2>',
                'body' => $articleBody,
            ]
        ]);
    }
}
