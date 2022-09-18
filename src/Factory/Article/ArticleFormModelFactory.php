<?php

namespace App\Factory\Article;

use App\Entity\Article;
use App\Factory\FactoryInterface;
use App\Form\Model\ArticleFormModel;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Фабрика генерации статей
 */
class ArticleFormModelFactory implements FactoryInterface
{
    /**
     * @throws Exception
     */
    public function createFromModel(object $model): ArticleFormModel
    {
        switch ($model) {
            case $model instanceof Article:
                return $this->createFromArticle($model);
            case $model instanceof Request:
                return $this->createFromRequest($model);
            default:
                throw new Exception('Get bad object form model type!');
        }
    }

    /**
     * Получает ДТО для полноценной генерации статьи
     * @throws Exception
     */
    public function createFromArticle(object $model): ArticleFormModel
    {
        $formModel = new ArticleFormModel();
        $formModel->id = $model->getId() ?? null;
        $formModel->theme = $model->getTheme();
        $formModel->title = $model->getTitle();
        $formModel->description = $model->getDescription();
        $formModel->sizeTo = $model->getSize();
        foreach ($model->getPromotedWords() as $promotedWord) {
            $formModel->promotedWords[] = $promotedWord['word'];
            $formModel->promotedWordCount[] = $promotedWord['count'];
        }
        foreach ($model->getKeyWord() as $keyWord) {
            $formModel->articleWords[] = $keyWord ?? '';
        }
        foreach ($model->getImages() as $image) {
            $formModel->images[] = $image;
        }


        return $formModel;
    }

    /**
     * Получает ДТО из рекввеста.
     * Используется для гененрации статьи посредством API
     */
    public function createFromRequest(object $model): ArticleFormModel
    {
        /** @var Request $model */
        $data = $model->toArray();
        $formModel = new ArticleFormModel();

        $formModel->theme = $data['theme'] ?? null;
        $formModel->description = $data['theme'] ?? null;
        $formModel->title = $data['title'] ?? null;
        $formModel->sizeTo = $data['sizeTo'] ?? null;
        $formModel->sizeFrom = $data['sizeFrom'] ?? null;
        foreach ($data['promotedWords'] as $promotedWord) {
            $formModel->promotedWords[] = $promotedWord['word'];
            $formModel->promotedWordCount[] = $promotedWord['count'];
        }
        foreach ($data['keyword'] as $morph) {
            $formModel->articleWords[] = $morph ?? null;
        }
        foreach ($data['images'] as $image) {
            $formModel->imageUrls[] = $image;
        }

        return $formModel;
    }
}
