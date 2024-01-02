<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\ReqHandler;

use App\Users\Domain\Dictionary\SecurityDictionary;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Service\UserDataHandlerInterface;
use App\Users\Infrastructure\Form\UserRegistrationFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterHandler
{
    public function __construct(
        private readonly UserDataHandlerInterface $registrationDataHandler,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function handleRegister(Request $request): array
    {
        $form = $this->formFactory->create(UserRegistrationFormType::class);
        $user = $this->registrationDataHandler->handleAndSaveUserData($request, $form, new User());

        return [
            'registrationForm' => $form->createView(),
            'success' => isset($user),
            // отдельно достаем ошибки, чтобы отобразить их над формой, параметр true используется для получения
            // ошибок отдельных полей
            'errors' => $form->getErrors(true),
            // Сообщение об ошибке при подтверждении email
            'confirmationError' => $request->query->get(SecurityDictionary::CONFIRMATION_EMAIL_FIELD),
        ];
    }
}
