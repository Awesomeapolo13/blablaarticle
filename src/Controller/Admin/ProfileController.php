<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * Отображает страницу профиля пользователя
     *
     * @Route("/admin/profile", name="app_admin_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(): Response
    {
        /**
         * ToDo:
         *      1) Внедрить форму регистрации для изменения информации в профиле (т.к. поля идентичны)
         *          Попробовать внедрить повторяющееся поле как в документации симфони
         *      2) Добавить в таблицу пользователя поле API токена, добавить в фикстуры генерацию этого токена
         *      3) Реализовать частичное изменение данных, не измененные данные в БД отправляться не должны
         */

        return $this->render('admin/profile/index.html.twig', []);
    }
}