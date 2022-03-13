<?php

namespace App\Form;

use App\Form\Model\UserRegistrationFormModel;
use App\Validator\UniqueUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Класс формы для регистрации пользователя и изменения его персональных данных
 */
class UserRegistrationFormType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Задаем валидацию для полей пароля и его подтверждения
        $passwordRequired = false;
        $passwordConstraints = [];
        $emailConstraints = [];
        // Пробуем получить текущего авторизованного пользователя
        $user = $this->security->getUser();
        if (!isset($user)) {
            // Если не можем получить пользователя, значит регистрируется впервые, необходимо включить дополнительную валидацию этих полей
            $passwordRequired = true;
            $passwordConstraints[] = new NotBlank(['message' => 'Введите пароль']);
            $emailConstraints = [
                'constraints' => [
                    new UniqueUser(),
                ]
            ];
        }

        $builder
            ->add('firstName', TextType::class)
            ->add('email', EmailType::class, $emailConstraints)
            ->add('planePassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Пароль не подтвержден',
                'required' => $passwordRequired,
                'constraints' => $passwordConstraints,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class,
        ]);
    }
}
