<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\ReqHandler;

use App\Users\Application\DTO\UserRegistrationFormModel;
use App\Users\Domain\Dictionary\ProfileDictionary;
use App\Users\Domain\Service\UserDataHandlerInterface;
use App\Users\Infrastructure\Form\UserRegistrationFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileHandler
{
    public function __construct(
        private readonly UserDataHandlerInterface $changeUserDataHandler,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function handleProfile(Request $request, UserInterface $user): array
    {
        $isConfirmed = $request->query->get('isConfirmed');
        // Ошибка подтверждения электронной почты
        $confirmationError = $request->query->get('confirmationError');

        // Создаем DTO для передачи в форму
        $userModel = UserRegistrationFormModel::create($user->getFirstName(), $user->getEmail());
        $form = $this->formFactory->create(UserRegistrationFormType::class, $userModel);
        $user = $this->changeUserDataHandler->handleAndSaveUserData($request, $form, $user);
        $errors = $form->getErrors(true);


        return [
            'userForm' => $form->createView(),
            'errors' => $errors,
            'success' => isset($user),
            'isConfirmed' => $isConfirmed,
            'confirmationError' => $confirmationError,
            'token' => $user->getApiToken(),
            'expiredMessage' => ProfileDictionary::EXPIRED_TOKEN_MSG,
        ];
    }
}
