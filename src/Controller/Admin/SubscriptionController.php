<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * Отображает страницу с информацией о подписке пользователя
     *
     * @Route("/admin/subscription", name="app_admin_subscription" )
     * @param Request $request
     * @return Response
     */
    public function index(
        Request $request
    ): Response
    {
        /**
         * ToDo:
         *      1) Создать сущность subscription, подумать что в нее должно входить.
         *          - title
         *          - price
         *          - opportunities
         *          Последний это json с ключами name:, isEnabled:, description:
         *      2) Создать миграцию для таблицы подписки
         *      3) Составить фикстуры для подписок
         *      4) Выполнить вывод всех подписок из базы на страницу и ограничить доступ к админке для неавторизованных
         *          пользователей
         *      5) Реализовать механизм изменения подписки в соостветствии с ТЗ
         */

        return $this->render('admin/subscription/index.html.twig', []);
    }
}