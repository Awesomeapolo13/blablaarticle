<?php

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие регистрации пользователя
 */
class UserRegisteredEvent extends Event
{
    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
