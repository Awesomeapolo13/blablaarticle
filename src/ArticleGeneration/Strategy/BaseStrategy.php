<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWord\PromotedWordInserter;
use App\Entity\Module;
use App\Repository\ModuleRepository;
use ArticleThemeProvider\ArticleThemeBundle\ThemeFactory;
use Faker\Factory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Базовая стратегия для генерации статьи
 */
abstract class BaseStrategy implements ArticleGenerationInterface
{
    /**
     * Сервис вставки продвигаемых слов
     */
    private PromotedWordInserter $wordInserter;

    /**
     * Сервис для рендеринга контента в шаблон
     */
    private Environment $twig;

    private ModuleRepository $moduleRepository;
    private ThemeFactory $themeFactory;

    public function __construct(
        PromotedWordInserter $wordInserter,
        Environment          $twig,
        ModuleRepository     $moduleRepository,
        ThemeFactory        $themeFactory
    ) {
        $this->wordInserter = $wordInserter;
        $this->twig = $twig;
        $this->moduleRepository = $moduleRepository;
        $this->themeFactory = $themeFactory;
    }

    /**
     * Заполняет плейсхолдеры сгенерированным текстом
     *
     * @param Module[] $modules
     * @return array - массив тел модулей с заполненными плейсхолдерами
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function fillPlaceholders(array $modules): array
    {
        $faker = Factory::create();
        $articleBody = [];
        foreach ($modules as $module) {
            $data = [];
            if (preg_match('/{{(\s)*?title?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['title'] = $faker->sentence();
            }

            if (preg_match('/{{(\s)*?paragraph?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['paragraph'] = $faker->paragraph(rand(1, 10));
            }

            if (preg_match('/{{(\s)*?paragraphs?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['paragraphs'] = '';
                foreach ($faker->paragraphs(rand(2, 7)) as $paragraph) {
                    $data['paragraphs'] .= PHP_EOL . '<p class="lead mb-0">' . $paragraph . '</p>';
                }
            }
            // ToDo Добавить imagePath и keyWord после того как простоим файловую систему
            $articleBody[] = $this->getTwig()->render('article/components/article_module.html.twig', [
                'data' => $data,
                'module' => $module
            ]);
        }

        return $articleBody;
    }

    /**
     * Базовый абстрактный метод генерации статьи
     */
    public abstract function generate(object $articleDTO);

    protected function getTwig(): Environment
    {
        return $this->twig;
    }

    protected function getWordInserter(): PromotedWordInserter
    {
        return $this->wordInserter;
    }

    protected function getModuleRepository(): ModuleRepository
    {
        return $this->moduleRepository;
    }

    protected function getThemeFactory(): ThemeFactory
    {
        return $this->themeFactory;
    }
}
