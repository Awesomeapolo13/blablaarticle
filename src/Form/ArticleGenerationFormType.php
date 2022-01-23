<?php

namespace App\Form;

use App\Form\Model\ArticleFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleGenerationFormType extends AbstractType
{
    private $morphLabels = [
        'Ключевое слово',
        'Родительный падеж',
        'Дательный падеж',
        'Винительный падеж',
        'Творительный падеж',
        'Предложный падеж',
        'Множественное число',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', ChoiceType::class, [
                'label' => 'Тематика',
                'choices' => [
                    '-' => '',
                    'Демонстрационная' => 'demo',
                    'Язык программирования PHP' => 'php',
                    'Домашние животные' => 'pets',
                    'Смысл жизни' => 'meaning_of_life'
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Заголовок статьи',
                'required' => false,
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'Заголовок статьи',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('sizeFrom', TextType::class, [
                'label' => 'Размер статьи от',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Размер статьи от',
                ],
            ])
            ->add('sizeTo', TextType::class, [
                'label' => 'До',
                'required' => false,
                'attr' => [
                    'placeholder' => 'До',
                ],
            ])
            ->add('images', FileType::class, [
                'label' => 'Изображения',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'placeholder' => 'Выбрать файлы',
                    'class' => 'form-control-file',
                ]
            ])
            ->add('articleWords', CollectionType::class, [
                'data' => ['', '', '', '', '', '', '',],
                'entry_type' => TextType::class,
            ])
            ->add('promotedWords', CollectionType::class, [
                'data' => [''],
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => 'Продвигаемое слово',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Продвигаемое слово'
                    ],
                ],
            ])
            ->add('promotedWordCount', CollectionType::class, [
                'data' => [''],
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'кол-во',
                'entry_options' => [
                    'label' => 'кол-во',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'кол-во'
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleFormModel::class,
        ]);
    }
}
