<?php

namespace App\Controller;

use App\ArticleGeneration\ArticleGenerator;
use App\ArticleGeneration\GenerationBlocker;
use App\Factory\Article\ArticleFactory;
use App\Form\ArticleDemoGenerateFormType;
use App\Form\Model\ArticleDemoFormModel;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * Страница демонстрационной генерации статьи
     *
     * Выводит страницу демонстрационной генерации статьи, обрабатывает форму демо-генерации и выводит ее результат
     *
     * @Route("/article", name="app_article_demo")
     * @throws Exception
     */
    public function index(
        Request                $request,
        EntityManagerInterface $em,
        ArticleRepository      $articleRepository,
        ArticleGenerator       $articleGenerator,
        ArticleFactory         $articleFactory,
        GenerationBlocker      $blocker
    ): Response
    {
        // Id статьи, получаем из куки, если она существует
        $articleId = $request->cookies->get('articleId');
        // Если задана кука с идентификатором статьи, то находим ее
        !empty($articleId) ? $article = $articleRepository->findOneBy(['id' => $articleId]) : $article = null;
        // Создаем объект ответа, чтобы задать куку
        $response = new Response();
        // Заголовок статьи по умолчанию
        $form = $this->createForm(ArticleDemoGenerateFormType::class);

        $form->handleRequest($request);
        // Если статья с указанным в куке id не найдена, то проверяем форму и создаем новую статью
        if (!isset($articleId) && $form->isSubmitted() && $form->isValid()) {
            /** @var ArticleDemoFormModel $articleDemoModel */
            $articleDemoModel = $form->getData();
            $article = $articleFactory->createFromModel($articleDemoModel);
            $article
                ->setBody(
                    $articleGenerator->generateArticle($article)
                )
            ;
            $em->persist($article);
            $em->flush();
            // Записываем в куки id статьи
            $articleId = $article->getId();
            $response = $blocker->blockDemo($response, $articleId);
        }

        return $this->render('article/index.html.twig',
            [
                'articleDemoGenerateForm' => $form->createView(),
                'article' => $article,
                'articleId' => $articleId
            ],
            $response
        );
    }
}
