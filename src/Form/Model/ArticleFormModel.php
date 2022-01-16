<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Класс DTO для формы генерации статьи
 */
class ArticleFormModel
{
    /**
     * Тематика
     *
     * @Assert\NotBlank(message="Укажите тематику статьи")
     * @var string
     */
    private $theme;

    /**
     * Заголовок статьи
     *
     * ToDo: Написать валидатор (или другой сервис) формирующий заголовок из темы
     * @var string
     */
    private $title;

    /**
     * Ключевое слово - именительный падеж
     *
     * @var string
     */
    private $article0Word;

    /**
     * Ключевое слово - родительный падеж
     *
     * @var string
     */
    private $article1Word;

    /**
     * Ключевое слово - дательный падеж
     *
     * @var string
     */
    private $article2Word;

    /**
     * Ключевое слово - винительный падеж
     *
     * @var string
     */
    private $article3Word;

    /**
     * Ключевое слово - творительный падеж
     *
     * @var string
     */
    private $article4Word;

    /**
     * Ключевое слово - предложный падеж
     *
     * @var string
     */
    private $article5Word;

    /**
     * Ключевое слово - множественное число
     *
     * @var string
     */
    private $article6Word;

    /**
     * Краткое описание статьи
     *
     * @Assert\LessThanOrEqual(255)
     * @var string
     */
    private $description;

    /**
     * Начало диапазона модулей
     *
     * ToDO: Валидация по диапазону модулей. Если указано лишь одно поле, то брать его
     *          если же нет, то необходимо сравнение со свойством ниже
     *
     * @var int
     */
    private $sizeFrom;

    /**
     * Конец диапазона модулей
     *
     * ToDO: Валидация по диапазону модулей. Не должен превышать количество модулей по дефолту
     *
     * @var int
     */
    private $sizeTo;

    /**
     * Продвигаемое слово - первое
     *
     * ToDO: Узнать каково максимальное количество продвигаемых слов
     *
     * @var string
     */
    private $promoted1Word;

    /**
     * Количество повторений первого продвигаемого слова
     *
     * @var int
     */
    private $promoted1WordCount;

    /**
     * Продвигаемое слово - второе
     *
     * ToDO: Узнать каково максимальное количество продвигаемых слов
     *
     * @var string
     */
    private $promoted2Word;

    /**
     * Количество повторений второго продвигаемого слова
     *
     * ToDO: Поле имеет смысл, только если указано само продвигаемое слово
     *
     * @var int
     */
    private $promoted2WordCount;

    /**
     * Продвигаемое слово - третье
     *
     * ToDO: Узнать каково максимальное количество продвигаемых слов
     *
     * @var string
     */
    private $promoted3Word;

    /**
     * Количество повторений третьего продвигаемого слова
     *
     * @var int
     */
    private $promoted3WordCount;

    /**
     * Изображения для статьи
     *
     * @var
     */
    private $image;
}