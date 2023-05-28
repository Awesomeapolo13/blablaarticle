<?php

namespace App\Event;

use App\Users\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие изменения подписки пользователем
 */
class UserChangeSubscriptionEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
