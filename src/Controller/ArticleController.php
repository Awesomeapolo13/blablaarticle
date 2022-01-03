<?php

namespace App\Controller;

use App\ArticleGeneration\ArticleGenerator;
use App\ArticleGeneration\PromotedWordInserter;
use App\ArticleGeneration\Strategy\DemoArticleGenerationStrategy;
use App\Entity\Article;
use App\Form\ArticleDemoGenerateFormType;
use App\Form\Model\ArticleDemoFormModel;
use Doctrine\ORM\EntityManagerInterface;
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
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /**
         * ToDo:
         *      Если внешний ключ модуля пустой, то это один из дефолтных модулей для генерации
         *      1) Для генерации статей использовать паттерн Стратегия.
         *      Разбить генерацию на 4 стратегии по виду подписки:
         *          - демонстрационная
         *          - FREE
         *          - PLUS
         *          - PRO
         *      Т.к. для генерации мы используем фейкер, не нужно ли его переместить в обычные зависимости?
         *      Нет плейсхолдеров для отображения заголовков внутри модулей. Эти заголовки должны добавляться
         *      из тематик?
         *      Как обратиться к первому элементу массива из формы симфони?
         *      Исправить тип колонки body на json, чтоб хранить в нем разные поля для вставки в модули.
         *      2) Реализовать сервис вставки продвигаемого слова в полученные абзацы
         *      3) Реализовать стратегию демонстрационной генерации статьи
         *      4) Реализовать блокировку формы после отправки
         *          - повторную отправку блокировать кукой с id статьи этой статьи (спросить преподавателя про
         *          использование ip в БД для привязки к незарегистрированному пользователю)
         *          - попробовать еще раз создать тему формы, т.к. оформление полей такое же как в регистрации
         */
        // Новая сгенерированная статья
        $article = null;
        // Заголовок статьи по умолчанию
        $form = $this->createForm(ArticleDemoGenerateFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleDemoFormModel  $articleDemoModel */
            $articleDemoModel = $form->getData();
            $articleGenerator = new ArticleGenerator(
                new DemoArticleGenerationStrategy($articleDemoModel, new PromotedWordInserter())
            );

            $newArticle = $articleGenerator->generateArticle();

            $article = Article::create(
                $newArticle['theme'],
                $newArticle['title'],
                $newArticle['size'],
                $newArticle['promotedWords'],
                $newArticle['content']
            );

            $em->persist($article);
            $em->flush();
//            dd($newArticle);
        }

        return $this->render('article/index.html.twig', [
            'articleDemoGenerateForm' => $form->createView(),
            'article' => $article,
        ]);
    }
}
