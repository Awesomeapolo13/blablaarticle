<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер отвечающий за работу с профилем пользователя
 */
class ProfileController extends AbstractController
{
    /**
     * Отображает страницу профиля пользователя
     *
     * @Route("/admin/profile", name="app_admin_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(Request $request): Response
    {
        /**
         * ToDo:
         *      1) Внедрить форму регистрации для изменения информации в профиле (т.к. поля идентичны)
         *          Попробовать внедрить повторяющееся поле как в документации симфони
         *      2) Добавить в таблицу пользователя поле API токена, добавить в фикстуры генерацию этого токена
         *      3) Реализовать частичное изменение данных, не измененные данные в БД отправляться не должны
         */

        $user = $this->getUser();

        $userModel = UserRegistrationFormModel::create($user->getFirstName(), $user->getEmail());

        $form = $this->createForm(UserRegistrationFormType::class, $userModel);

        $form->handleRequest($request);

        $errors = $form->getErrors(true);

        return $this->render('admin/profile/index.html.twig', [
            'userForm' => $form->createView(),
            'errors' => $errors,
        ]);
    }
}