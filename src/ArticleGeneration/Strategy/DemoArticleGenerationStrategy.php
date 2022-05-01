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
     * Количество продвигаемых слов для демонстрационной генерации статьи
     */
    public const PROMO_WORD_COUNT = 1;

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
        // ToDO Вытаскиевает три одинаковых модуля. Надо либо сделать случайный выбор модулей, либо сделать
        //  первые три всегда для демо
        $modules = $this->moduleRepository->findDefaultWithLimit(3);

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
            $articleBody[] = $this->twig->render('article/components/article_module.html.twig', [
                'data' => $data,
                'module' => $module
            ]);
        }

        // ToDo Тут надо вставить продвигаемое слово и проверить как это все будет работать
        for ($i = 1; $i <= 1; $i++) {
            // Выбираем случайный модуль через случайную позицию
            $randModulePos = rand(0, count($articleBody) - 1);
            // Выбираем текст из случайного модуля, он будет в массиве под ключом 1 $matches
            if (
                preg_match_all(
                    '/(?:<\w+\d?(?:.*)?>)(.+)?(?:<\/\w+\d?>)/',
                    $articleBody[$randModulePos],
                    $matches
                )
            ) {
                // Выбираем случайный текст в модуле
                $targetText = $matches[1][rand(0, count($matches[1]) - 1)];
                $textArr = explode(' ', $targetText);
                array_splice($textArr, rand(0, count($textArr) - 2), 0, $articleDTO->promotedWord);
                $targetText = mb_substr($targetText, 0, 30);
                // ToDO  Вероятно вставит текст до первого закрывающего тега, что не правильно. Придумать как этого избежать
                $articleBody[$randModulePos] = preg_replace(
                    "/($targetText.*?)<\/\w+\d?>/",
                    implode(' ', $textArr),
                    $articleBody[$randModulePos]
                );
            }
        }

        return $this->twig->render('article/components/article_demo.html.twig', [
            'article' => [
                'title' => '<h2 class="card-title text-center mb-4">' . $articleDTO->title . '</h2>',
                'body' => $articleBody,
            ]
        ]);
    }
}
