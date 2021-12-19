<?php

namespace App\Controller;

use App\Form\ArticleDemoGenerateFormType;
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
         *      1) Создать форму для демонстрации генерации статьи
         *          - создать сам объект формы и DTO для него
         *          - повторную отправку блокировать кукой с id статьи этой статьи (спросить преподавателя про
         *          использование ip в БД для привязки к незарегистрированному пользователю)
         *          - попробовать еще раз создать тему формы, т.к. оформление полей такое же как в регистрации
         *      2) Для генерации статей использовать паттерн Стратегия.
         *      Разбить генерацию на 4 стратегии по виду подписки:
         *          - демонстрационная
         *          - FREE
         *          - PLUS
         *          - PRO
         *      Сейчас реализовать только демонстрационную стратегию
         *      3) Создать в БД таблицу для хранения статей, она должна содержать:
         *          - продвигаемое слово;
         *          - ip компьютера, с которого сделан запрос (чтобы нельзя было сделать его дважды или сделать бесконечную куку?)
         *          - тематику пока сделать массивом
         *      4) Реализовать сервис вставки продвигаемого слова в полученные абзацы
         *          Как получить абзацы для демонстрационной генерации (с помощью DiDom? брать их в БД в качестве модулей? со статусом demo?
         *      5) Реализовать блокировку формы после отправки
         */

        $form = $this->createForm(ArticleDemoGenerateFormType::class);

        $form->handleRequest($request);

        dd($form->getData());

        return $this->render('article/index.html.twig', [
            'articleDemoGenerateForm' => $form->createView(),
        ]);
    }
}
