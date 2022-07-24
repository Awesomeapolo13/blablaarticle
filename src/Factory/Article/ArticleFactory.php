<?php

namespace App\Factory\Article;

use App\Entity\Article;
use App\Entity\ArticleImage;
use App\Factory\FactoryInterface;
use App\Form\Model\ArticleDemoFormModel;
use App\Form\Model\ArticleFormModel;
use App\Repository\UserRepository;
use ArticleThemeProvider\ArticleThemeBundle\ThemeFactory;
use Exception;

/**
 * Фабрика генерации статей
 */
class ArticleFactory implements FactoryInterface
{
    private ThemeFactory $themeFactory;
    private UserRepository $userRepository;

    public function __construct(
        ThemeFactory $themeFactory,
        UserRepository $userRepository
    )
    {
        $this->themeFactory = $themeFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     */
    public function createFromModel(object $model): object
    {
        switch ($model) {
            case $model instanceof ArticleFormModel:
                return $this->createFull($model);
            case $model instanceof ArticleDemoFormModel:
                return $this->createDemo($model);
            default:
                throw new Exception('Get bad Article form model type!');
        }
    }

    /**
     * Получает объект сущности статьи и приложенных к ней изображений
     *
     * @param ArticleFormModel $articleFormModel - ДТО статьи для полноценной генерации
     * @return Article
     */
    private function createFull(ArticleFormModel $articleFormModel): Article
    {
        $article = new Article();
        // сохраняем описание, если оно есть
        if (isset($articleFormModel->description)) {
            $article->setDescription($articleFormModel->description);
        }
        // Формируем массив продвигаемых слов
        if (!empty($articleFormModel->promotedWords)) {
            $promotedWords = [];
            foreach ($articleFormModel->promotedWords as $key => $promotedWord) {
                $promotedWords[] = [
                    'word' => $promotedWord,
                    'count' => $articleFormModel->promotedWordCount[$key],
                ];
            }
            $article->setPromotedWords($promotedWords);
        }
        // Формируем заголовок из тематики, если он не задан
        if (!isset($articleFormModel->title)) {
            $articleFormModel->title = $this->themeFactory
                ->findThemeBySlug($articleFormModel->theme)
                ->getName();
        }
        // Если определен один из параметров, то выбираем тот, что определен
        $size = $articleFormModel->sizeFrom ?? null;
        if (isset($articleFormModel->sizeTo)) {
            $size = $articleFormModel->sizeTo;
        }
        // Если определены оба, то выбираем рандомное количество модулей между полученными значений
        if (isset($articleFormModel->sizeFrom) && isset($articleFormModel->sizeTo)) {
            $size = rand($articleFormModel->sizeFrom, $articleFormModel->sizeTo);
        }
        // Получает имена изображений, сохраненных в файловой системе для этой статьи
        if (!empty($articleFormModel->images)) {
            foreach ($articleFormModel->images as $image) {
                $article->addImage(
                    (new ArticleImage())->setName($image)
                );
            }
        }

        return $article
            ->setTheme($articleFormModel->theme)
            ->setKeyWord($articleFormModel->articleWords)
            ->setTitle($articleFormModel->title)
            ->setSize($size)
            ;
    }

    /**
     * Получает объект сущности статьи при демонстрационной генерации ее тела
     *
     * @param ArticleDemoFormModel $articleDemoFormModel - ДТО статьи для демонстрационной генерации
     * @return Article
     */
    private function createDemo(ArticleDemoFormModel $articleDemoFormModel): Article
    {
        return (new Article())
            ->setTheme('demo')
            ->setKeyWord(['demonstration'])
            ->setTitle($articleDemoFormModel->title)
            ->setSize(3)
            ->setPromotedWords(['word' => $articleDemoFormModel->promotedWord, 'count' => 1])
            ->setClient($this->userRepository->findOneBy(['id' => 1]))
            ;
    }
}
