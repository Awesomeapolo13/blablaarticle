<?php

namespace App\Security\Service\UserDataHandler;

use App\Entity\ApiToken;
use App\Event\UserRegisteredEvent;
use App\Form\Model\UserRegistrationFormModel;
use App\Repository\SubscriptionRepository;
use App\Security\Service\UserDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Обработчик пользовательских данных для регистрации пользователей
 */
class RegistrationDataHandler implements UserDataHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(
        EntityManagerInterface       $em,
        EventDispatcherInterface     $dispatcher,
        UserPasswordEncoderInterface $passwordEncoder,
        SubscriptionRepository       $subscriptionRepository
    )
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->passwordEncoder = $passwordEncoder;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Обработка пользовательских данных для регистрации пользователя и их сохранение
     *
     * @param Request $request - запрос, содержащий данные из формы для обработки
     * @param FormInterface $form - форма для обработки и валидации данных
     * @param UserInterface $user - сущность пользователя для сохранения в БД
     * @return UserInterface|void - вернет объект пользователя, либо null, если форма не валидна
     */
    public function handleAndSaveUserData(Request $request, FormInterface $form, UserInterface $user)
    {
        // обрабатываем запрос
        $form->handleRequest($request);
        // если форма отправлена и данные ее валидны, начинаем их обработку
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();

            $subscription = $this->subscriptionRepository->findOneBy(['name' => 'FREE']);

            $user
                ->setFirstName($userModel->firstName)
                ->setEmail($userModel->email)
                ->setPassword($this->passwordEncoder->encodePassword($user, $userModel->planePassword))
                ->setIsEmailConfirmed(false)
                ->setExpireAt(new \DateTime('+1 week'))
                ->setSubscription($subscription)
                ->setApiToken(ApiToken::create($user))
            ;

            $this->em->persist($user);
            $this->em->flush();

            $this->dispatcher->dispatch(new UserRegisteredEvent($user));

            return $user;
        }
    }
}
