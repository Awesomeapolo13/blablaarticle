<?php

namespace App\EventSubscriber;

use App\Event\UserChangeSubscriptionEvent;
use App\Mailer\BaseMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Подписчик события регистрации нового пользователя
 */
class UserChangeSubscriptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var BaseMailer
     */
    private $mailer;

    public function __construct(BaseMailer $subscriptionMailer)
    {
        $this->mailer = $subscriptionMailer;
    }

    /**
     * Вызыввет email рассылку при изменении пользователем подписки
     *
     * @throws TransportExceptionInterface
     */
    public function onUserChangeSubscription(UserChangeSubscriptionEvent $event): void
    {
        $this->mailer->sendUserChangeSubscriptionLetter(
            $event->getUser()
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserChangeSubscriptionEvent::class => 'onUserChangeSubscription',
        ];
    }
}
