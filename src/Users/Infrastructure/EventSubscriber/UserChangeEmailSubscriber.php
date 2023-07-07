<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\EventSubscriber;

use App\Mailer\BaseMailer;
use App\Users\Domain\Event\UserChangeEmailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик события регистрации нового пользователя
 */
class UserChangeEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BaseMailer $confirmEmailMailer)
    {
    }

    /**
     * Вызывает email-рассылку для подтверждения почты пользователя после ее изменения через профиль
     */
    public function onUserEmailChanged(UserChangeEmailEvent $event): void
    {
        $this->confirmEmailMailer->sendConfirmEmailLetter(
            $event->getUser(),
            'Подтвердите изменение электронной почты',
            base64_encode(json_encode([
                'email' => $event->getUser()->getEmail(),
                'newEmail' => $event->getNewEmail(),
            ])),
            'confirm_email_after_change.email.template',
            $event->getNewEmail()
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserChangeEmailEvent::class => 'onUserEmailChanged',
        ];
    }
}
