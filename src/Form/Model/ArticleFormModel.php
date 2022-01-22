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
     * @Assert\NotBlank(message="Введите ключевое слово")
     * @var array
     */
    private $articleWords;

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
     * Продвигаемых слов может быть сколько угодно. Добавление поля происходит кнопкой
     *
     * @var string
     */
    private $promotedWord;

    /**
     * Количество повторений первого продвигаемого слова
     *
     * @var int
     */
    private $promotedWordCount;

    /**
     * Изображения для статьи
     *
     * @var
     */
    private $image;
}