<?php

declare(strict_types=1);

namespace App\Users\Domain\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие изменения подписки пользователем
 */
class UserChangeSubscriptionEvent extends Event
{
    public function __construct(private readonly UserInterface $user)
    {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
