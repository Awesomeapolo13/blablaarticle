<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixtures
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
            ['ROLE_PLUS_SUBSCRIBER']
        );

        $this->createUser(
            $manager,
            'proSubscriber@mail.ru',
            'Nadezhda',
            ['ROLE_PRO_SUBSCRIBER']
        );

        $this->createMany(User::class, 10, function (User $user) use ($manager) {
            $user
                ->setEmail($this->faker->email)
                ->setFirstName($this->faker->firstName)
                ->setPassword($this->passwordEncoder->encodePassword($user, '123456'))
            ;

            $manager->persist($user);
        });
    }

    /**
     * Метод создания одного юзера
     *
     * Для создания юзера с конкретными данными, например для админа или другой роли
     *
     * @param ObjectManager $manager
     * @param string $email
     * @param string $firstname
     * @param string $password
     * @param array $roles
     */
    private function createUser(
        ObjectManager $manager,
        string        $email,
        string        $firstname,
        array         $roles = [],
        string        $password = '123456'

    ): void
    {
        $this->create(User::class, function (User $user) use (
            $manager,
            $email,
            $firstname,
            $password,
            $roles
        ) {
           $user
               ->setEmail($email)
               ->setFirstName($firstname)
               ->setRoles($roles)
               ->setPassword($this->passwordEncoder->encodePassword($user, $password))
           ;

           $manager->persist($user);
        });
    }
}
