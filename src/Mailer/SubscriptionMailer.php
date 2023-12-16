<?php

namespace App\Mailer;

use App\Users\Domain\Entity\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Класс рассылки email писем, касающихся подписок
 */
class SubscriptionMailer extends BaseMailer
{
    /**
     * Отправляет email письмо при изменении пользователем своей подписки
     *
     * @param User $user
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sendUserChangeSubscriptionLetter(User $user)
    {
        $this->send(
            $this->params->get('user_change_subscription.email.template'),
            'Изменение уровня подписки',
            $user,
            function (TemplatedEmail $email) use ($user) {
                $email->context([
                    'user' => $user,
                ]);
            }
        );
    }
}
