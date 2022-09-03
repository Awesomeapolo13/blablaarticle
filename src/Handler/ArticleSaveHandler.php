<?php

namespace App\Handler;

use App\ArticleGeneration\ArticleGenerator;
use App\Entity\Article;
use App\Factory\Article\ArticleFactory;
use App\Form\Model\ArticleFormModel;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Flysystem\FilesystemException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Обработчик запросов для статей
 */
class ArticleSaveHandler
{
    private FileUploader $fileUploader;
    private ArticleFactory $articleFactory;
    private ArticleGenerator $articleGenerator;
    private EntityManagerInterface $em;

    public function __construct(
        FileUploader           $fileUploader,
        ArticleFactory         $articleFactory,
        ArticleGenerator       $articleGenerator,
        EntityManagerInterface $em
    )
    {
        $this->fileUploader = $fileUploader;
        $this->articleFactory = $articleFactory;
        $this->articleGenerator = $articleGenerator;
        $this->em = $em;
    }

    /**
     * Обрабатывает запрос на сохранение статьи
     * @param FormInterface $form - форма с данными для обработки
     * @param bool $isBlocked - параметр указывающий на блокировку функционала генерации новых статей
     * @param UserInterface $user
     * @return Article|null
     * @throws FilesystemException
     * @throws Exception
     */
    public function handleAndSave(
        FormInterface $form,
        UserInterface $user,
        bool $isBlocked
    ): ?Article {
        if ($form->isSubmitted() && $form->isValid() && !$isBlocked) {
            /** @var ArticleFormModel $articleModel */
            $articleModel = $form->getData();
            // Сохраняем изображения и записываем их имена в свойство ДТО
            $articleModel->images = $this->fileUploader->uploadManyFiles($articleModel->images);
            // Передаем ДТО в фабрику статей для формирования объекта статьи
            $article = $this->articleFactory
                ->createFromModel($articleModel);
            $article
                ->setClient($user)
                ->setBody(
                    $this->articleGenerator
                        ->generateArticle($article)
                );
            $this->em->persist($article);
            $this->em->flush();

            return $article;
        }

        return null;
    }
}