<?php

namespace App\Controller\Admin;

use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Security\Service\UserDataHandlerInterface;
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
     * @throws \Exception
     */
    public function index(
        Request                      $request,
        UserDataHandlerInterface     $changeUserDataHandler
    ): Response
    {
        /**
         * ToDo:
         *      1) Добавить в таблицу пользователя поле API токена, добавить в фикстуры генерацию этого токена
         *      2) Реализовать частичное изменение данных, не измененные данные в БД отправляться не должны
         *          - не изменять пароль, если он не указан
         *          - подтверждение нового email отправлять только при его изменении
         *          - новый email устанавливается только после подтверждения
         *      !! Зарегистрировать новые сервисы пользователей
         *      Можно передавать текущего пользователя в сервис по сохранению данных пользователя
         *      и сравнивать их там. Если такой пользователь не найден, то сравнения просто не будет
         *      Поместить в метод конфигурации формы логику для выбора того или иного DTO, либо сделать декоратор
         */

        $user = $this->getUser();
        // Если нельзя найти авторизованного пользователя, то прервать выполнение метода
        if (!isset($user)) {
            throw new \Exception('User is not found');
        }

        $userModel = UserRegistrationFormModel::create($user->getFirstName(), $user->getEmail());

        $form = $this->createForm(UserRegistrationFormType::class, $userModel);

        $user = $changeUserDataHandler->handleAndSaveUserData($request, $form, $user);

        $success = isset($user);

        $errors = $form->getErrors(true);

        return $this->render('admin/profile/index.html.twig', [
            'userForm' => $form->createView(),
            'errors' => $errors,
            'success' => $success,
        ]);
    }
}