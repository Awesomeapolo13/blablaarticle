<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Класс voter для пользователей подтвердивших почту
 */
class EmailConfirmedVoter extends Voter
{
    /**
     * Определяет применяется ли voter к запрашиваемому правилу
     *
     * @param string $attribute - переданное правило
     * @param $subject - переданная сущность
     * @return bool - вернет true, если правило подходит
     */
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'IS_EMAIL_CONFIRMED';
    }

    /**
     * Проверка соответствия правила
     *
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool - вернет true, если у пользователя есть доступ к ресурсу
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Проверяем аутентифицирован ли пользователь
        /** @var UserInterface $subject */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Проверяем правило
        switch ($attribute) {
            case 'IS_EMAIL_CONFIRMED':
                if ($user->getIsEmailConfirmed()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
