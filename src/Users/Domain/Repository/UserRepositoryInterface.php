<?php

declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Users\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * Метод получения всех пользователем с истекшим сроком подписки кроме FREE
     *
     * @return User[]
     */
    public function findAllExpiredUsers(): array;

    /**
     * Сохраняет пользователя.
     */
    public function save(User $user): void;
}
