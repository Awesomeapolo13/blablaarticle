<?php

namespace App\Form;

use App\Form\Model\ArticleFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        ;
        // добавляем поля для ключевого слова и его словоформ
        for ($i = 0; $i < 7; $i++) {
            $builder->add('article' . $i . 'Word', TextType::class, [
                'label' => !empty($this->morphLabels[$i]) ? $this->morphLabels[$i] : '',
                'required' => false,
                'attr' => [
                    'placeholder' => !empty($this->morphLabels[$i]) ? $this->morphLabels[$i] : '',
                ],
            ]);
        }

        for ($i = 1; $i < 4; $i++) {
            $builder
                ->add('promoted' . $i . 'Word', TextType::class, [
                    'label' => 'Продвигаемое слово',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Продвигаемое слово'
                    ]
                ])
                ->add('promoted' . $i . 'WordCount', TextType::class, [
                    'label' => 'кол-во',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'кол-во'
                    ]
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleFormModel::class,
        ]);
    }
}
