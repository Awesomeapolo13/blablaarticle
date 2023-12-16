<?php

namespace App\Users\Domain\Service;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Интерфейс для сервисов обработки и сохранения пользовательских данных
 */
interface UserDataHandlerInterface
{
    /**
     * Обрабатывает и сохраняет пользовательские данные
     *
     * @param Request $request - запрос, содержащий данные из формы для обработки
     * @param FormInterface $form - форма для обработки и валидации данных
     * @param UserInterface $user - сущность пользователя для сохранения в БД
     * @return UserInterface|void - вернет объект пользователя, либо null, если форма не валидна
     */
    public function handleAndSaveUserData(Request $request, FormInterface $form, UserInterface $user);
}
