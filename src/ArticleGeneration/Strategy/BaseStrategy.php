<?php

namespace App\ArticleGeneration\Strategy;

use App\ArticleGeneration\ArticleGenerationInterface;
use App\ArticleGeneration\PromotedWord\PromotedWordInserter;
use App\Entity\Article;
use App\Entity\Module;
use App\Repository\ModuleRepository;
use App\Twig\AppUploadedAsset;
use ArticleThemeProvider\ArticleThemeBundle\ThemeFactory;
use Exception;
use Faker\Factory;
use Faker\Generator;
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
     * Базовая генерация статьи
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(Article $article): string
    {
        $articleBody = $this->prepareArticleBody($article);

        return $this->renderArticleBody($article->getTitle(), $articleBody);
    }

    /**
     * Метод формирования данных для тела статьи.
     * Может быть переопределен для конкретной стратегии
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function prepareArticleBody(Article $article): array
    {
        /** @var Module[] $modules */
        $modules = $this->getModules($article);
        // Заполняем статью контентом
        $articleBody = $this->fillPlaceholders($modules, $article);
        // Заполняем статью контентом тематик
        $articleBody = $this->addThemeContent($article, $articleBody);
        // Вставка продвигаемых слов
        $articleBody = $this->addPromotedWords($article, $articleBody);

        return $articleBody;
    }

    /**
     * Формирует статью из переданного заголовка и тела
     * @param string $title - заголовок статьи
     * @param array $articleBody - массив заполненных модулей для статьи
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function renderArticleBody(string $title, array $articleBody): string
    {
        return $this->getTwig()->render('article/components/article_body.html.twig', [
            'article' => [
                'body' => $articleBody,
            ]
        ]);
    }

    /**
     * Возвращает модули участвующие в процессе генерации статьи
     * По умолчанию возвращает дефолтные модули. Может быть переопределен в других стратегиях
     *
     * @param Article $article
     * @return array
     */
    protected function getModules(Article $article): array
    {
        return $this->getModuleRepository()
            ->findDefaultWithLimit(
                $article->getSize()
            );
    }

    /**
     * Заполняет плейсхолдеры сгенерированным текстом
     * @param Module[] $modules
     * @return array - массив тел модулей с заполненными плейсхолдерами
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function fillPlaceholders(
        array $modules,
        Article $article,
        array $articleBody = []
    ): array {
        $faker = Factory::create();
        // Массив изображений записываем в переменную, чтобы использовать все переданные изображения минимум один раз
        $minImagesArr = (clone $article->getImages())->toArray();
        // перемешаем модули чтобы шли в случайном порядке
        shuffle($modules);
        foreach ($modules as $module) {
            $data = [];
            // Заполняем заголовки
            $data['title'] = $this->generateTitle($module->getBody(), $faker);
            // Заполняем одиночные параграфы
            $data['paragraph'] = $this->generateParagraph($module->getBody(), $faker);
            // Заполняем наборы параграфов
            $data['paragraphs'] = $this->generateParagraphs($module->getBody(), $faker);
            // Заполняем ссылки на изображения
            $data['imageSrc'] = $this->generateImgPath(
                $module->getBody(),
                $article->getImages()->toArray(),
                $minImagesArr
            );

            $data['keyword'] = $this->resolveKeyWord(
                $article->getKeyWord()
            );

            $articleBody[] = $this
                ->getTwig()
                ->render(
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
     * Генерирует текст для плейсхолдера title
     *
     * @param string $targetText - текст для осуществления поиска
     * @param Generator $faker - генератор текста для заполнения
     * @return string - текст заголовка для последующей вставки в модуль
     */
    protected function generateTitle(string $targetText, Generator $faker): string
    {
        return preg_match('/\{\{(\s)*?title?(\|raw)?(\s)*?}}/', $targetText)
        ?
            $faker->sentence()
            :
            '';
    }

    /**
     * Генерирует текст для плейсхолдера paragrahp
     *
     * @param string $targetText - текст для осуществления поиска
     * @param Generator $faker - генератор текста для заполнения
     * @return string - текст параграфа для последующей вставки в модуль
     */
    protected function generateParagraph(string $targetText, Generator $faker): string
    {
        return preg_match('/\{\{(\s)*?paragraph?(\|raw)?(\s)*?}}/', $targetText)
            ?
            $faker->paragraph(rand(1, 10))
            :
            '';
    }

    /**
     * Генерирует текст для плейсхолдера paragrahps
     *
     * @param string $targetText - текст для осуществления поиска
     * @param Generator $faker - генератор текста для заполнения
     * @return string - текст параграфов для последующей вставки в модуль
     */
    protected function generateParagraphs(string $targetText, Generator $faker): string
    {
        $paragraphs = '';
        if (preg_match('/\{\{(\s)*?paragraphs?(\|raw)?(\s)*?}}/', $targetText)) {
            foreach ($faker->paragraphs(rand(2, 7)) as $paragraph) {
                $paragraphs .= PHP_EOL . '<p class="lead mb-0">' . $paragraph . '</p>';
            }
        }

        return $paragraphs;
    }

    /**
     * Заполняет путь к изображению для статьи
     *
     * @param string $targetText - текст для осуществления поиска
     * @param array $imgArr - массив всех изображений
     * @param array $minImagesArr - массив изображений, которые обязательно надо вставить хотя бы один раз
     * @return string
     */
    protected function generateImgPath(
        string $targetText,
        array $imgArr,
        array &$minImagesArr
    ): string {
        $imageSrc = 'https://via.placeholder.com/250x250';
        if (preg_match('/\{\{(\s)*?imageSrc?(\|raw)?(\s)*?}}/', $targetText) && !empty($imgArr)) {
            // Выбираем рандомное изображение
            $image = $imgArr[array_rand($imgArr)];
            // Если image является url, то вставляем его без пути к папке
            $config = filter_var($image, FILTER_VALIDATE_URL) === false
                ?
                'article_uploads_url'
                :
                'article_uploads_img_url';
            // Достаиваем путь до изображения путем из конфига
            $imageSrc = $this->getUploadedAsset()
                    ->asset(
                        $config,
                        $image
                    );
            // Если хотя бы одно изображение еще не было использовано единожды, берем его
            if (!empty($minImagesArr)) {
                $key = array_rand($minImagesArr);
                $imageSrc = $this->getUploadedAsset()
                    ->asset(
                        $config,
                        $minImagesArr[$key]
                    );
                unset($minImagesArr[$key]);
            }
        }

        return $imageSrc;
    }

    /**
     * По умолчанию возвращаем массив ключевых слов
     * @param array $keyWords
     * @return array
     */
    protected function resolveKeyWord(array $keyWords): array
    {
        return $keyWords;
    }

    /**
     * Дополняет тело статьи контентом, полученным из Тематик
     *
     * @param Article $article
     * @param array $articleBody - сформированный массив для построения статьи.
     * Представляет собой массив отрендеренных модулей, содержащих сгенерированный контент.
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    protected function addThemeContent(
        Article $article,
        array $articleBody
    ): array {
        $theme = $this->getThemeFactory()
            ->findThemeBySlug(
                $article->getTheme()
            );
        if (!$theme) {
            throw new Exception('Тематика не найдена', 400);
        }

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

        return $articleBody;
    }

    /**
     * Дополняет тело статьи продвигаемыми словами
     *
     * @param Article $article
     * @param array $articleBody - сформированный массив для построения статьи.
     * Представляет собой массив отрендеренных модулей, содержащих сгенерированный контент.
     * @return array
     */
    protected function addPromotedWords(
        Article $article,
        array $articleBody
    ): array {
        foreach ($article->getPromotedWords() as $promotedWord) {
            $articleBody = $this->getWordInserter()->paste(
                $articleBody,
                $promotedWord['word'],
                $promotedWord['count']
            );
        }

        return $articleBody;
    }

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
}
