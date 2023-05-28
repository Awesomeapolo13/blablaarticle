<?php

namespace App\Mailer;

use App\Users\Domain\Entity\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;

/**
 * Сервис отправки сообщений для подтверждения пароля
 */
class ConfirmEmailMailer extends BaseMailer
{
    /**
     * @param User $user - модель зарегистрированного пользователя
     * @param string $subject - тема письма
     * @param string $hashEmail - данные лоя подтверждения email
     * @param string $emailTemplate - шаблон используемый для отправки письма
     * @param string $confirmationEmail - электронная почта, которую нужно подтвердить
     * (указывается если почта отличается от той, что в объекте пользователя)
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sendConfirmEmailLetter(
        User   $user,
        string $subject,
        string $hashEmail,
        string $emailTemplate,
        string $confirmationEmail = ''
    ): void
    {
        $this->send(
            $this->params->get($emailTemplate),
            $subject,
            $user,
            function (TemplatedEmail $email) use ($user, $hashEmail, $confirmationEmail) {
                if (!empty($confirmationEmail)) {
                    $email->to(new Address($confirmationEmail, $user->getFirstName()));
                }

                $email->context([
                    'hashEmail' => $hashEmail,
                    'userName' => $user->getFirstName(),
                ]);
            }
        );
    }
}
