<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWordInserter;
use App\Form\Model\ArticleDemoFormModel;
use Faker\Factory;

/**
 * Стратегия для демонстрационной генерации статьи
 *
 * ToDo: доработать генерацию, после того как будет реализован функционал тематик, модулей и прочее
 */
class DemoArticleGenerationStrategy implements ArticleGenerationInterface
{
    private $addYourWordsModule = '<div class="row">
                        <div class="p-3">
                            <h2>{{title}}</h2>
                            <p class="lead mb-0">{{ paragraph }}</p>
                        </div>
                    </div>';

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

    private $apiModule = '<div class="row">
                        <div class="p-3">
                            <h2>{{ title }}</h2>
                            <p class="lead mb-0">{{ paragraph }}</p>
                        </div>
                    </div>';

    /**
     * @var PromotedWordInserter - сервис для вставки продвигаемого слова
     */
    private $wordInserter;
    /**
     * @var string
     */
    private $articleDemoFormModel;

    public function __construct(ArticleDemoFormModel $articleDemoFormModel, PromotedWordInserter $wordInserter)
    {
        $this->articleDemoFormModel = $articleDemoFormModel;
        $this->wordInserter = $wordInserter;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $faker = Factory::create();

        $addWordsParagraph = $this->wordInserter->pasteWordIntoText(
            $this->articleDemoFormModel->promotedWord,
            $faker->paragraph()
        );
        $putImagesParagraphs = $this->wordInserter->pasteWordIntoParagraphs(
            $this->articleDemoFormModel->promotedWord,
            $faker->paragraphs(2)
        );
        $useApiParagraph = $this->wordInserter->pasteWordIntoText(
            $this->articleDemoFormModel->promotedWord,
            $faker->paragraph(4)
        );
        $putImagesParagraphsStr = null;
        // вставляем параграфы из массива в теги
        foreach ($putImagesParagraphs as $paragraph) {
            $putImagesParagraphsStr .= '<p class="lead mb-0">' . $paragraph . '</p>';
        }

        return [
            'title' => $this->articleDemoFormModel->title,
            'content' => [
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
                    'module' => $this->apiModule,
                ],
            ]
        ];
    }
}
