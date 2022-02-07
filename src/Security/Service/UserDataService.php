<?php

namespace App\Security\Service;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataService
{
    /**
     * Обрабатывает и сохраняет пользовательские данные
     *
     * @param Request $request
     * @param FormInterface $form
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @param SubscriptionRepository $subscriptionRepository
     * @return User|void
     */
    public function handleAndSaveUserData(
        Request                      $request,
        FormInterface                $form,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface       $em,
        SubscriptionRepository       $subscriptionRepository
    )
    {
        // обрабатываем запрос
        $form->handleRequest($request);

        // если форма отправлена и данные ее валидны, начинаем их обработку
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();
            $user = new User();
            $subscription = $subscriptionRepository->findOneBy(['name' => 'FREE']);

            $user
                ->setFirstName($userModel->email)
                ->setEmail($userModel->email)
                ->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $userModel->planePassword
                ))
                ->setIsEmailConfirmed(false)
                ->setExpireAt(new \DateTime('+1 week'))
                ->setSubscription($subscription);

            $em->persist($user);
            $em->flush();

            return $user;
        }
    }
}
