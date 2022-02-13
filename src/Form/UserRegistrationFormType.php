<?php

namespace App\Form;

use App\Form\Model\UserRegistrationFormModel;
use App\Validator\UniqueUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        $passwordConstraints = ['required' => false];
        $confirmPasswordConstraints = ['required' => false];
        $emailConstraint = [];
        // Пробуем получить текущего авторизованного пользователя
        $user = $this->security->getUser();
        if (!isset($user)) {
            // Если не можем получить пользователя, значит регистрируется впервые, необходимо включить дополнительную валидацию этих полей
            $passwordConstraints = [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Введите пароль']),
                ]
            ];
            $confirmPasswordConstraints = [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Введите пароль для подтверждения']),
                ]
            ];
            $emailConstraint = [
                'constraints' => [
                    new UniqueUser(),
                ]
            ];
        }

        $builder
            ->add('firstName', TextType::class)
            ->add('email', EmailType::class, $emailConstraint)
            ->add('planePassword', PasswordType::class, $passwordConstraints)
            ->add('confirmPassword', PasswordType::class, $confirmPasswordConstraints)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class,
        ]);
    }
}
