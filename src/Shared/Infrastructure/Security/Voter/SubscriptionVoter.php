<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *  Класс Voter для проверки уровня подписки пользователя
 */
class SubscriptionVoter extends Voter
{
    /**
     * Определяет, применяется ли voter к запрашиваемому правилу
     *
     * @param string $attribute - переданное правило
     * @param $subject - переданная сущность
     * @return bool - вернет true, если правило подходит
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['IS_PRO_SUBSCRIBER', 'IS_PLUS_SUBSCRIBER'], true);
    }

    /**
     * Проверка соответствия правила
     *
     * @return bool - вернет true, если у пользователя есть доступ к ресурсу
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            'IS_PRO_SUBSCRIBER' => $this->isSubscriberType('PRO', $user) || $this->isAdmin($user),
            'IS_PLUS_SUBSCRIBER' => $this->isSubscriberType('PLUS', $user) || $this->isAdmin($user),
            default => false,
        };

    }

    /**
     * Проверяет, является ли пользователь владельцем переданной подписки
     *
     * @param string $subscriptionName - имя подписки
     * @return bool - возвращает true, если есть совпадение с переданной подпиской
     */
    private function isSubscriberType(string $subscriptionName, UserInterface $user): bool
    {
       return ($user->getSubscription())->getName() === $subscriptionName;
    }

    /**
     * Проверяет, является ли пользователь администратором
     */
    private function isAdmin(UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN' ,$user->getRoles());
    }
}
