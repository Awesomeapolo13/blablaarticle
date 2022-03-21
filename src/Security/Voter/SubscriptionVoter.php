<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *  Класс Voter для проверки уровня подписки пользователя
 */
class SubscriptionVoter extends Voter
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
        return in_array($attribute, ['IS_PRO_SUBSCRIBER', 'IS_PLUS_SUBSCRIBER'], true);
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
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'IS_PRO_SUBSCRIBER':
                return $this->isSubscriberType('PRO', $user);
            case 'IS_PLUS_SUBSCRIBER':
                return $this->isSubscriberType('PLUS', $user);
        }

        return false;
    }

    /**
     * Проверяет, является ли пользователь владельцем переданной подписки
     *
     * @param string $subscriptionName - имя подписки
     * @param UserInterface $user - пользователь
     * @return bool|void - возвращает true, если
     */
    private function isSubscriberType(string $subscriptionName, UserInterface $user)
    {
        if (($user->getSubscription())->getName() === $subscriptionName) {
            return true;
        }
    }
}
