<?php

namespace App\Security\Service;

use App\Event\UserRegisteredEvent;
use App\Form\Model\UserRegistrationFormModel;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserDataService
{
    /**
     * Сервис для вызова события для изменения почты (для подтверждения)
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Сервис хеширования паролей
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->dispatcher = $dispatcher;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Обрабатывает и сохраняет пользовательские данные
     *
     * При изменении данных пользователя срабатывают только сеттеры изменяемых полей
     *
     * @param Request $request
     * @param FormInterface $form
     * @param EntityManagerInterface $em
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserInterface $user
     * @return UserInterface
     */
    public function handleAndSaveUserData(
        Request                      $request,
        FormInterface                $form,
        EntityManagerInterface       $em,
        SubscriptionRepository       $subscriptionRepository,
        UserInterface                $user
    )
    {
//        dd($this->isNewEntity($user));

        /** @var UserRegistrationFormModel $userModel */
        $userModel = $form->getData();
        // Если новый пользователь (регистрация), то задаем все свойства и сохраняем пользователя
        if ($this->isNewEntity($user)) {
            $subscription = $subscriptionRepository->findOneBy(['name' => 'FREE']);

            $user
                ->setFirstName($userModel->firstName)
                ->setEmail($userModel->email)
                ->setPassword($this->passwordEncoder->encodePassword($user, $userModel->planePassword))
                ->setIsEmailConfirmed(false)
                ->setExpireAt(new \DateTime('+1 week'))
                ->setSubscription($subscription)
            ;

            $em->persist($user);
            $em->flush();

            $this->dispatcher->dispatch(new UserRegisteredEvent($user));

            return $user;
        }
        // Проверяем изменено ли имя
        if ($this->isPropertyChanged($user->getFirstName(), $userModel->firstName)) {
            dump('The property $firstName is changed');
            $user->setFirstName($userModel->firstName);
        }
        // Проверяем изменен ли пароль
        if (!empty($userModel->planePassword) && $this->isPropertyChanged($user->getPassword(), $this->passwordEncoder->encodePassword($user, $userModel->planePassword))) {
            dump('The property $password is changed');
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userModel->planePassword));
        }

        $em->persist($user);
        $em->flush();
        // Проверяем изменена ли электронная почта
        if ($this->isPropertyChanged($user->getEmail(), $userModel->email)) {
            dump('The property $email will change');
            // ToDo: Добавить вызов события подтверждения почты
            ($request->getSession())
                ->set('newEmail', $userModel->email);
//            $this->dispatcher->dispatch(new UserRegisteredEvent($user));
        }

        return $user;
    }

    /**
     * Проверяет, является ли переданная в сервис сущность новой
     *
     * Сущность считается новой, если все ее свойства пустые, не имеющие значений по умолчанию, пусты
     *
     * @param UserInterface $user
     * @param array $except - массив имен свойств, которые не нужно проверять на пустоту (т.к. у них есть значения по умолчанию)
     * @return bool - возвращает true если сущность новая, false - в противном случае
     */
    private function isNewEntity(UserInterface $user, array $except = []): bool
    {
        $propertiesArr = (array)$user;

//        dd($propertiesArr);

        foreach ($propertiesArr as $propName => $property) {
            if (!empty($property) && !in_array($propName, $except, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверяет, изменено ли свойство объекта
     *
     * @param $entityPropValue - значение свойства объекта сущности
     * @param $DTOProdValue - значение этого свойства, пришедшее из формы
     * @return bool - возвращает true, когда полученное из формы значение существует и отличается от хранимого в БД
     */
    private function isPropertyChanged($entityPropValue, $DTOProdValue): bool
    {
        // ToDo: УБРАТЬ ВСЕ ДАМПЫ
        dump($entityPropValue);
        dump($DTOProdValue);
        return !empty($DTOProdValue) && ($entityPropValue !== $DTOProdValue);
    }
}
