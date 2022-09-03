<?php

namespace App\Form;

use App\Form\Model\ArticleFormModel;
use ArticleThemeProvider\ArticleThemeBundle\Theme;
use ArticleThemeProvider\ArticleThemeBundle\ThemeFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Класс формы создания статьи
 */
class ArticleGenerationFormType extends AbstractType
{
    /**
     * Фабрика тематик
     */
    private ThemeFactory $themeFactory;

    public function __construct(ThemeFactory $themeFactory)
    {
        $this->themeFactory = $themeFactory;
    }

    /**
     * Строит форму создания статьи
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ArticleFormModel $article */
        $article = $options['data'] ?? null;
        // Тематики
        $themes = ['-' => ''];
        // Ключевые слова
        $keywords = [];
        // Продвигаемые слова и их количество
        $promotedWords = [];
        $promotedWordsCount = [];
        // Добавляем тематики для выведения в форме
        foreach ($this->themeFactory->getThemes() as $theme) {
            /** @var Theme $theme */
            if ('demo' !== $theme->getSlug())
                $themes[$theme->getName()] = $theme->getSlug();
        }
        // Определяем ключевые слова и сеттим из если есть
        for ($i = 0; $i <= 6; $i++) {
            $keywords[] = ($article->articleWords)[$i] ?? '';
        }
        // Определяем продвигаемые слова и их количество, если они заданы
        if (!empty($article->promotedWords) && !empty($article->promotedWordCount)) {
            foreach ($article->promotedWords as $key => $word) {
                $promotedWords[] = $word;
                $promotedWordsCount[$key] = ($article->promotedWordCount)[$key];
            }
        }

        $builder
            ->add('theme', ChoiceType::class, [
                'label' => 'Тематика',
                'choices' => $themes,
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
                    'errors' => false,
                ],
                'label_attr' => [
                    'errors' => false
                ],
            ])
            ->add('sizeFrom', IntegerType::class, [
                'label' => 'Размер статьи от',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Размер статьи от',
                ],
            ])
            ->add('sizeTo', IntegerType::class, [
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
                ],
            ])
            ->add('articleWords', CollectionType::class, [
                'data' => $keywords,
                'entry_type' => TextType::class,
            ])
            ->add('promotedWords', CollectionType::class, [
                'data' => $promotedWords ?: [''],
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
                'data' => $promotedWordsCount === [] ? [0] : $promotedWordsCount,
                'entry_type' => IntegerType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'кол-во',
                'entry_options' => [
                    'label' => 'кол-во',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'кол-во',
                    ],
                ],
            ])
        ;
    }

    /**
     * Конфигурация опций
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleFormModel::class,
        ]);
    }
}
