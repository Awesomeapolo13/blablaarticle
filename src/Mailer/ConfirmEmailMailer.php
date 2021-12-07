<?php

namespace App\Mailer;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Сервис отправки сообщений для подтверждения пароля
 */
class ConfirmEmailMailer extends BaseMailer
{
    /**
     * @param User $user - модель зарегистрированного пользователя
     * @throws TransportExceptionInterface
     */
    public function sendConfirmEmailLetter(User $user): void
    {
        $this->send(
            $this->params->get('confirm_email.email.template'),
            'Подтвердите email для завершения регистрации',
            $user
        );
    }
}
