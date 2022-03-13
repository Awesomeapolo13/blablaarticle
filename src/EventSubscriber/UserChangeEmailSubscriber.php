<?php

namespace App\EventSubscriber;

use App\Event\UserChangeEmailEvent;
use App\Mailer\BaseMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик события регистрации нового пользователя
 */
class UserChangeEmailSubscriber implements EventSubscriberInterface
{
    /**
     * @var BaseMailer
     */
    private $mailer;

    public function __construct(BaseMailer $confirmEmailMailer)
    {
        $this->mailer = $confirmEmailMailer;
    }

    /**
     * Вызывает email-рассылку для подтверждения почты пользователя после ее изменения через профиль
     */
    public function onUserEmailChanged(UserChangeEmailEvent $event): void
    {
        $this->mailer->sendConfirmEmailLetter(
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
