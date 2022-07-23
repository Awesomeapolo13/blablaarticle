<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWord\PromotedWordInserter;
use App\Entity\Article;
use App\Entity\Module;
use App\Repository\ModuleRepository;
use App\Twig\AppUploadedAsset;
use ArticleThemeProvider\ArticleThemeBundle\ThemeFactory;
use Faker\Factory;
use Symfony\Component\Security\Core\User\UserInterface;
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
    private UserInterface $user;
    private AppUploadedAsset $uploadedAsset;

    public function __construct(
        PromotedWordInserter $wordInserter,
        Environment          $twig,
        ModuleRepository     $moduleRepository,
        ThemeFactory         $themeFactory,
        AppUploadedAsset     $uploadedAsset
    ) {
        $this->wordInserter = $wordInserter;
        $this->twig = $twig;
        $this->moduleRepository = $moduleRepository;
        $this->themeFactory = $themeFactory;
        $this->uploadedAsset = $uploadedAsset;
    }

    /**
     * Заполняет плейсхолдеры сгенерированным текстом
     * ToDO Сделать статью обязательным аргументом. Переопределить метод внутри стратегии для демонстрации
     * @param Module[] $modules
     * @return array - массив тел модулей с заполненными плейсхолдерами
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function fillPlaceholders(array $modules, Article $article = null): array
    {
        $faker = Factory::create();
        $articleBody = [];
        // Массив изображений записываем в переменную, чтобы использовать все переданные изображения минимум один раз
        $minImagesArr = (clone $article->getImages())->toArray();
        // перемешаем модули чтобы шли в случайном порядке
        shuffle($modules);
        foreach ($modules as $module) {
            $data = [];
            // Заполняем заголовки
            if (preg_match('/{{(\s)*?title?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['title'] = $faker->sentence();
            }
            // Заполняем одиночные параграфы
            if (preg_match('/{{(\s)*?paragraph?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['paragraph'] = $faker->paragraph(rand(1, 10));
            }
            // Заполняем наборы параграфов
            if (preg_match('/{{(\s)*?paragraphs?(\|raw)?(\s)*?}}/', $module->getBody())) {
                $data['paragraphs'] = '';
                foreach ($faker->paragraphs(rand(2, 7)) as $paragraph) {
                    $data['paragraphs'] .= PHP_EOL . '<p class="lead mb-0">' . $paragraph . '</p>';
                }
            }
            // Заполняем ссылки на изображения
            if (preg_match('/{{(\s)*?imageSrc?(\|raw)?(\s)*?}}/', $module->getBody())) {
                // Выбираем рандомное изображение
                $data['imageSrc'] = $article->getImages()[array_rand($article->getImages()->toArray())];
                // Если хотя бы одно изображение еще не было использовано единожды, берем его
                if (!empty($minImagesArr)) {
                    $key = array_rand($minImagesArr);
                    $data['imageSrc'] = $this->getUploadedAsset()
                        ->asset(
                            'article_uploads_url',
                            $minImagesArr[$key]
                        );
                    unset($minImagesArr[$key]);
                }
            }

            $data['keyword'] = $article->getKeyWord();

            $articleBody[] = $this->getTwig()->render(
                'article/components/article_module.html.twig',
                [
                    'data' => $data,
                    'module' => $module
                ]
            );
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

    public function getUploadedAsset(): AppUploadedAsset
    {
        return $this->uploadedAsset;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }
}
