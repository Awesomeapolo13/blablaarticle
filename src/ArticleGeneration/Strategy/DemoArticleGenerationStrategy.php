<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWordInserter;
use Faker\Factory;
use Twig\Environment;

/**
 * Стратегия для демонстрационной генерации статьи
 *
 * ToDo: доработать генерацию, после того как будет реализован функционал тематик, модулей и прочее
 */
class DemoArticleGenerationStrategy implements ArticleGenerationInterface
{
    /**
     * @var string - модуль с одним параграфом
     */
    private $addYourWordsModule = '<div class="row">
                        <div class="p-3">
                            <h2>{{title}}</h2>
                            <p class="lead mb-0">{{ paragraph }}</p>
                        </div>
                    </div>';

    /**
     * @var string - модуль с картинкой и несколькими параграфами
     */
    private $pasteImagesModule = '<div class="row">
                        <div class="showcase-text p-3">
                            <div class="media">
                                <div class="media-body">
                                    <h2>{{ title }}</h2>
                                    {{paragraphs|raw}}
                                </div>
                                <img class="ml-3" src="{{ asset("image/bg-showcase-2.jpg") }}" width="518" height="345"
                                     alt="Демонстрационная картинка">
                            </div>

                        </div>
                    </div>';

    /**
     * @var PromotedWordInserter - сервис вставки продвигаемых слов
     */
    private $wordInserter;

    /**
     * @var Environment - сервис для рендеринга контента в шаблон
     */
    private $twig;

    public function __construct(PromotedWordInserter $wordInserter, Environment $twig)
    {
        $this->wordInserter = $wordInserter;
        $this->twig = $twig;
    }

    /**
     * Генерирует демонстрационную статьи
     *
     * @param object $articleDTO
     * @return string - вложенный массив для сохранения с данными новой статьи
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generate(object $articleDTO): string
    {
        $faker = Factory::create();

        $addWordsParagraph = $this->wordInserter->pasteWordIntoText(
            $articleDTO->promotedWord,
            $faker->paragraph()
        );
        $putImagesParagraphs = $this->wordInserter->pasteWordIntoParagraphs(
            $articleDTO->promotedWord,
            $faker->paragraphs(2)
        );
        $useApiParagraph = $this->wordInserter->pasteWordIntoText(
            $articleDTO->promotedWord,
            $faker->paragraph(4)
        );
        $putImagesParagraphsStr = null;
        // вставляем параграфы из массива в теги
        foreach ($putImagesParagraphs as $paragraph) {
            $putImagesParagraphsStr .= '<p class="lead mb-0">' .$paragraph. '</p>';
        }

        $articleData = [
            'title' => '<h2 class="card-title text-center mb-4">' .$articleDTO->title. '</h2>',
            'description' => 'Статья сгенерированная для демонстрации функционала генерации статей',
            'theme' => 'demo',
            'size' => 3,
            'promotedWords' => [
                ['word' => $articleDTO->promotedWord, 'count' => 1],
            ],
            'body' => [
                [
                    'title' => $faker->sentence(3),
                    'paragraph' => $addWordsParagraph,
                    'module' => $this->addYourWordsModule,
                ],
                [
                    'title' => $faker->sentence(2),
                    'paragraphs' => $putImagesParagraphsStr,
                    'module' => $this->pasteImagesModule,
                ],
                [
                    'title' => $faker->sentence(3),
                    'paragraph' => $useApiParagraph,
                    'module' => $this->addYourWordsModule,
                ],
            ]
        ];

        return $this->twig->render('article/components/article_demo.html.twig', ['article' => $articleData]);
    }
}
