<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\ReqHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginHandler
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils
    ) {
    }

    public function handleLogin(Request $request): array
    {
        return [
            // последний логин
            'last_username' => $this->authenticationUtils->getLastUsername(),
            // достает текст последней ошибки авторизации
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'confirmationError' => $request->query->get('confirmationError'),
        ];
    }
}
