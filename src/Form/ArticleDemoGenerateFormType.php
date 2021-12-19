<?php

namespace App\Form;

use App\Form\Model\ArticleDemoFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма демонстрационного создания статьи
 */
class ArticleDemoGenerateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                //  задал тут, чтобы потом изменить при отображении
                'attr' => [
                    'value' => 'Тестовая статья'
                ]
            ])
            ->add('promotedWord', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleDemoFormModel::class,
        ]);
    }
}
