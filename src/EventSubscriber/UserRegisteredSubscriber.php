<?php

namespace App\EventSubscriber;

use App\Event\UserRegisteredEvent;
use App\Mailer\BaseMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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
     *
     * @throws TransportExceptionInterface
     */
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->mailer->sendConfirmEmailLetter(
            $event->getUser(),
            base64_encode(json_encode(['email' => $event->getUser()->getEmail()]))
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }
}
