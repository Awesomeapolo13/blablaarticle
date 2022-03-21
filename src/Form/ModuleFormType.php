<?php

namespace App\Form;

use App\Form\Model\ModuleFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма создания модулей
 */
class ModuleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название модуля',
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'Название модуля'
                ],
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Код модуля',
                'attr' => [
                    'raws' => 2,
                    'errors' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModuleFormModel::class,
        ]);
    }
}
