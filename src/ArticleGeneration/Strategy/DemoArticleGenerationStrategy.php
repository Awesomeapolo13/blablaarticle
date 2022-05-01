<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWord\PromotedWordInserter;
use App\Entity\Module;
use App\Repository\ModuleRepository;
use Faker\Factory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Стратегия для демонстрационной генерации статьи
 *
 * ToDo: доработать генерацию, после того как будет реализован функционал тематик, модулей и прочее
 */
class DemoArticleGenerationStrategy implements ArticleGenerationInterface
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
     * @var PromotedWordInserter - сервис вставки продвигаемых слов
     */
    private PromotedWordInserter $wordInserter;

    /**
     * @var Environment - сервис для рендеринга контента в шаблон
     */
    private Environment $twig;

    /**
     * @var ModuleRepository
     */
    private ModuleRepository $moduleRepository;

    public function __construct(
        PromotedWordInserter $wordInserter,
        Environment          $twig,
        ModuleRepository     $moduleRepository
    )
    {
        $this->wordInserter = $wordInserter;
        $this->twig = $twig;
        $this->moduleRepository = $moduleRepository;
    }

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
        $faker = Factory::create();
        /** @var Module[] $modules */
        // ToDO Вытаскивает три одинаковых модуля. Надо либо сделать случайный выбор модулей, либо сделать
        //  первые три всегда для демо
        $modules = $this->getModuleRepository()->findDefaultWithLimit(self::DEMO_MODULES_COUNT);

        $articleBody = [];
        foreach ($modules as $module) {
            $data = [];
            if (preg_match('/{{(\s)*?title?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['title'] = $faker->sentence();
            }

            // Todo В принципе p почти везде одинаков, значит можно сделать вставку чисто в текст параграфов. Остальное мимо
            if (preg_match('/{{(\s)*?paragraph?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['paragraph'] = $faker->paragraph(rand(1, 10));
            }

            if (preg_match('/{{(\s)*?paragraphs?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['paragraphs'] = '';
                foreach ($faker->paragraphs(rand(2, 7)) as $paragraph) {
                    $data['paragraphs'] .= PHP_EOL . '<p class="lead mb-0">' . $paragraph . '</p>';
                }
            }
            // ToDo Добавить imagePath после того как простоим файловую систему
            $articleBody[] = $this->getTwig()->render('article/components/article_module.html.twig', [
                'data' => $data,
                'module' => $module
            ]);
        }

        $articleBody = $this->getWordInserter()->paste(
            $articleBody,
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

    private function getWordInserter(): PromotedWordInserter
    {
        return $this->wordInserter;
    }

    private function getTwig(): Environment
    {
        return $this->twig;
    }

    private function getModuleRepository(): ModuleRepository
    {
        return $this->moduleRepository;
    }
}
