<?php

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие изменения почты пользователем через профиль
 */
class UserChangeEmailEvent extends Event
{
    /**
     * Пользователь изменивший email
     *
     * @var UserInterface
     */
    private $user;

    /**
     * Новая почта пользователя
     *
     * @var string
     */
    private $newEmail;

    public function __construct(UserInterface $user, string $newEmail)
    {
        $this->user = $user;
        $this->newEmail = $newEmail;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getNewEmail(): string
    {
        return $this->newEmail;
    }
}
