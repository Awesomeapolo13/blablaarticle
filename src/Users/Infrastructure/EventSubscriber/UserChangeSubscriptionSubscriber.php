<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\EventSubscriber;

use App\Mailer\BaseMailer;
use App\Users\Domain\Event\UserChangeSubscriptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Подписчик события регистрации нового пользователя
 */
class UserChangeSubscriptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BaseMailer $subscriptionMailer)
    {
    }

    /**
     * Вызыввет email рассылку при изменении пользователем подписки
     *
     * @throws TransportExceptionInterface
     */
    public function onUserChangeSubscription(UserChangeSubscriptionEvent $event): void
    {
        $this->subscriptionMailer->sendUserChangeSubscriptionLetter(
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
