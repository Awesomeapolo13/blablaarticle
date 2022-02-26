<?php

namespace App\EventSubscriber;

use App\Event\UserRegisteredEvent;
use App\Mailer\BaseMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик события регистрации нового пользователя
 */
class UserRegisteredSubscriber implements EventSubscriberInterface
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
     * Вызывает email-рассылку для подтверждения почты пользователя после регистрации
     */
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->mailer->sendConfirmEmailLetter(
            $event->getUser(),
            'Подтвердите email для завершения регистрации',
            base64_encode(json_encode(['email' => $event->getUser()->getEmail()])),
            'confirm_email_after_registration.email.template'
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }
}
