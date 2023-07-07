<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\EventSubscriber;

use App\Mailer\BaseMailer;
use App\Users\Domain\Event\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик события регистрации нового пользователя
 */
class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BaseMailer $confirmEmailMailer)
    {
    }

    /**
     * Вызывает email-рассылку для подтверждения почты пользователя после регистрации
     */
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->confirmEmailMailer->sendConfirmEmailLetter(
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
