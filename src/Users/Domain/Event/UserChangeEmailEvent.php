<?php

declare(strict_types=1);

namespace App\Users\Domain\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие изменения почты пользователем через профиль
 */
class UserChangeEmailEvent extends Event
{
    /**
     * @param UserInterface $user - пользователь изменивший email
     * @param string $newEmail - новая почта пользователя
     */
    public function __construct(
        private readonly UserInterface $user,
        private readonly string $newEmail
    ) {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getNewEmail(): string
    {
        return $this->newEmail;
    }
}
