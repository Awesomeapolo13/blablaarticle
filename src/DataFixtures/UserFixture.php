<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Users\Domain\Entity\ApiToken;
use App\Users\Domain\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixtures implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createUser(
            $manager,
            'plusSubscriber@mail.ru',
            'Petrusha',
            ['ROLE_ADMIN']
        );

        $this->createUser(
            $manager,
            'proSubscriber@mail.ru',
            'Nadezhda',
            ['ROLE_ADMIN']
        );

        $this->createMany(User::class, 10, function (User $user) use ($manager) {
            $user
                ->setEmail($this->faker->email)
                ->setFirstName($this->faker->firstName)
                ->setPassword($this->passwordEncoder->encodePassword($user, '123456'))
                ->setIsEmailConfirmed($this->faker->boolean(70))
                ->setSubscription($this->getRandomReference(Subscription::class))
                ->setExpireAt($this->faker->dateTimeThisYear('+1 week'))
            ;

            $manager->persist(ApiToken::create($user));
        });
    }

    /**
     * Метод создания одного юзера
     *
     * Для создания юзера с конкретными данными, например для админа или другой роли
     *
     * @param ObjectManager $manager
     * @param string $email - электронная почта пользователя
     * @param string $firstname - имя пользователя
     * @param array $roles - роли пользователя
     * @param string $password - пароль пользователя
     * @param bool $isEmailConfirmed - подтверждена ли электронная почта
     * @throws \Exception
     */
    private function createUser(
        ObjectManager $manager,
        string        $email,
        string        $firstname,
        array         $roles = [],
        string        $password = '123456',
        bool          $isEmailConfirmed = false

    ): void
    {
        $this->create(User::class, function (User $user) use (
            $manager,
            $email,
            $firstname,
            $password,
            $roles,
            $isEmailConfirmed
        ) {
           $user
               ->setEmail($email)
               ->setFirstName($firstname)
               ->setRoles($roles)
               ->setPassword($this->passwordEncoder->encodePassword($user, $password))
               ->setIsEmailConfirmed($isEmailConfirmed)
               ->setSubscription($this->getRandomReference(Subscription::class))
               ->setExpireAt($this->faker->dateTimeBetween('now', '+1 week'))
           ;

           $manager->persist(ApiToken::create($user));
           $manager->persist($user);
        });
    }

    public function getDependencies(): array
    {
        return [
            SubscriptionFixtures::class,
        ];
    }
}
