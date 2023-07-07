<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Service\UserDataHandler;

use App\Event\UserRegisteredEvent;
use App\Repository\SubscriptionRepository;
use App\Users\Application\DTO\UserRegistrationFormModel;
use App\Users\Domain\Entity\ApiToken;
use App\Users\Domain\Service\UserDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    public function __construct(
        private readonly EntityManagerInterface       $em,
        private readonly EventDispatcherInterface     $dispatcher,
        private readonly UserPasswordEncoderInterface $passwordEncoder,
        private readonly SubscriptionRepository       $subscriptionRepository
    ) {
    }

    /**
     * Обработка пользовательских данных для регистрации пользователя и их сохранение
     *
     * @param Request $request - запрос, содержащий данные из формы для обработки
     * @param FormInterface $form - форма для обработки и валидации данных
     * @param UserInterface $user - сущность пользователя для сохранения в БД
     * @return UserInterface|void - вернет объект пользователя, либо null, если форма не валидна
     * @throws Exception
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
