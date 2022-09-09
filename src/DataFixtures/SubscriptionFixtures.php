<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Persistence\ObjectManager;

/**
 * Класс фикстур для подписок
 */
class SubscriptionFixtures extends BaseFixtures
{
    /**
     * @var array[] - дефолтные подписки для фикстур
     */
    private $defaultSubscription = [
        [
            'name' => 'FREE',
            'price' => 0,
            'opportunities' => [
                [
                    'isEnabled' => true,
                    'description' => 'Возможность создать более 1 статьи',
                ],
                [
                    'isEnabled' => true,
                    'description' => 'Базовые возможности генератора',
                ],
                [
                    'isEnabled' => false,
                    'description' => 'Продвинутые возможности генератора',
                ],
                [
                    'isEnabled' => false,
                    'description' => 'Свои модули',
                ],
            ],
            'blockTime' => '60',
            'blockCount' => 2,
        ],
        [
            'name' => 'PLUS',
            'price' => 9,
            'opportunities' => [
                [
                    'isEnabled' => true,
                    'description' => 'Возможность создать более 1 статьи',
                ],
                [
                    'isEnabled' => true,
                    'description' => 'Базовые возможности генератора',
                ],
                [
                    'isEnabled' => true,
                    'description' => 'Продвинутые возможности генератора',
                ],
                [
                    'isEnabled' => false,
                    'description' => 'Свои модули',
                ],
            ],
            'blockTime' => '60',
            'blockCount' => 2,
        ],
        [
            'name' => 'PRO',
            'price' => 49,
            'opportunities' => [
                [
                    'isEnabled' => true,
                    'description' => 'Безлимитная генерация статей для вашего аккаунта',
                ],
                [
                    'isEnabled' => true,
                    'description' => 'Базовые возможности генератора',
                ],
                [
                    'isEnabled' => true,
                    'description' => 'Продвинутые возможности генератора',
                ],
                [
                    'isEnabled' => true,
                    'description' => 'Свои модули',
                ],
            ],
            'blockTime' => null,
            'blockCount' => null,
        ],
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function loadData(ObjectManager $manager)
    {
        foreach ($this->defaultSubscription as $key => $defaultSubscription) {
            $entity = $this->create(Subscription::class, function (Subscription $subscription) use ($manager, $defaultSubscription) {
                $subscription
                    ->setName($defaultSubscription['name'])
                    ->setPrice($defaultSubscription['price'])
                    ->setOpportunities($defaultSubscription['opportunities'])
                    ->setBlockTime($defaultSubscription['blockTime'])
                    ->setBlockCount($defaultSubscription['blockCount'])
                ;
            });

            $this->addReference( Subscription::class . "|$key", $entity);
        }

        $manager->flush();
    }
}