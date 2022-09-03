<?php

namespace App\Factory\Article;

use App\Entity\Article;
use App\Factory\FactoryInterface;
use App\Form\Model\ArticleFormModel;
use Exception;

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
        if (!($model instanceof Article)) {
            throw new Exception('Get bad Article for form type!');
        }

        $formModel = new ArticleFormModel();
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
}
