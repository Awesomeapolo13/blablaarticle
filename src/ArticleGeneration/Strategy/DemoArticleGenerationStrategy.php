<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWordInserter;
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
     * @var PromotedWordInserter - сервис вставки продвигаемых слов
     */
    private $wordInserter;

    /**
     * @var Environment - сервис для рендеринга контента в шаблон
     */
    private $twig;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

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
        $modules = $this->moduleRepository->findDefaultWithLimit(3);

        $articleBody = '';
        foreach ($modules as $module) {
            $data = [];
            if (preg_match('/{{(\s)*title?(\|raw)?(\s)*}}/', $module->getBody())) {
                $data['title'] = $faker->sentence();
            }

            // Todo В принципе p почти везде одинаков, значит можно сделать вставку чисто в текст параграфов. Остальное мимо
            if (preg_match('/{{(\s)*paragraph?(\|raw)?(\s)*}}/', $module->getBody())) {
                $data['paragraph'] = $faker->paragraph(rand(1, 10));
            }

            // ToDO Стоит так же конкатенировать в цикле параграфы
            if (preg_match('/{{(\s)*paragraphs?(\|raw)?(\s)*}}/', $module->getBody())) {
                foreach ($faker->paragraphs(rand(2, 7)) as $paragraph) {
                    $data['paragraphs'] .= '<p class="lead mb-0">' .$paragraph. '</p>';
                }
            }
            // ToDo Добавить imagePath после того как простоим файловую систему
            $articleBody .= $this->twig->render('article/components/article_module.html.twig', [
                'data' => $data,
                'module' => $module
            ]);
        }

        // ToDo Тут надо вставить продвигаемое слово и проверить как это все будет работать



        $articleData = [
            'title' => '<h2 class="card-title text-center mb-4">' . $articleDTO->title . '</h2>',
            'description' => 'Статья сгенерированная для демонстрации функционала генерации статей',
            'theme' => 'demo',
            'size' => 3,
            'promotedWords' => [
                ['word' => $articleDTO->promotedWord, 'count' => 1],
            ],
            'body' => $articleBody,
        ];

        return $this->twig->render('article/components/article_demo.html.twig', ['article' => $articleData]);
    }
}
