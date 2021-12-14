<?php

namespace App\Mailer;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
    public function sendConfirmEmailLetter(User $user, string $hashEmail): void
    {
        $this->send(
            $this->params->get('confirm_email.email.template'),
            'Подтвердите email для завершения регистрации',
            $user,
            function (TemplatedEmail $email) use ($hashEmail) {
                $email->context([
                    'hashEmail' => $hashEmail,
                ]);
            }
        );
    }
}
