<?php

namespace App\Controller;

use App\ArticleGeneration\ArticleGenerator;
use App\ArticleGeneration\PromotedWordInserter;
use App\ArticleGeneration\Strategy\DemoArticleGenerationStrategy;
use App\Form\ArticleDemoGenerateFormType;
use App\Form\Model\ArticleDemoFormModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article_demo")
     */
    public function index(Request $request): Response
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
         *      Исправить тип колонки body на json, чтоб хранить в нем разные поля для вставки в модули.
         *      2) Реализовать сервис вставки продвигаемого слова в полученные абзацы
         *      3) Реализовать стратегию демонстрационной генерации статьи
         *      4) Реализовать блокировку формы после отправки
         *          - повторную отправку блокировать кукой с id статьи этой статьи (спросить преподавателя про
         *          использование ip в БД для привязки к незарегистрированному пользователю)
         *          - попробовать еще раз создать тему формы, т.к. оформление полей такое же как в регистрации
         */
        // Новая сгенерированная статья
        $newArticle = null;
        // Заголовок статьи по умолчанию
        $title = 'Тестовая статья';
        $form = $this->createForm(ArticleDemoGenerateFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleDemoFormModel  $articleDemoModel */
            $articleDemoModel = $form->getData();
            $articleGenerator = new ArticleGenerator(new DemoArticleGenerationStrategy($articleDemoModel, new PromotedWordInserter()));
            $newArticle = $articleGenerator->generateArticle();
            $title = $newArticle['title'];
//            dd($newArticle);
        }

        return $this->render('article/index.html.twig', [
            'articleDemoGenerateForm' => $form->createView(),
            'title' => $title,
            'content' => $newArticle,
        ]);
    }
}
