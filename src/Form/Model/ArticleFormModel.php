<?php

namespace App\Form\Model;

use App\Validator\IsEmptyBoth;
use App\Validator\SizeRange;
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
    public $theme;

    /**
     * Заголовок статьи
     *
     * ToDo: Написать сервис формирующий заголовок из темы, если он не заполнен
     * @var string
     */
    public $title;

    /**
     * Ключевое слово
     *
     * Обязательной является форма именительного падежа (первое поле ввода).
     * Остальные словоформы указываются по желанию
     *
     * @Assert\NotBlank(message="Введите ключевое слово")
     * @var array
     */
    public $articleWords;

    /**
     * Краткое описание статьи
     *
     * @Assert\Length(
     *     min=0,
     *     max=255,
     *     maxMessage="Описание должно быть кратким и не превышать 255 символов")
     * @var string
     */
    public $description;

    /**
     * Начало диапазона модулей
     *
     * ToDO: После добавления функционала модулей, добавить валидацию по максимальному размеру статьи не больше
     *  количества дефолтный модулей. Пока указать не больше 3-х. Возможно стоит добавить help текст у поля формы
     *    или указывать максимальное количество в поле ввода заранее.
     *
     * @Assert\Type("integer", message="Минимальное количество модулей для генерации статьи должно быть числом")
     * @Assert\Range(
     *     min=0,
     *     max=10,
     *     minMessage="Количество модулей для генерации статьи не может быть отрицательной величиной",
     *     maxMessage="Количество модулей для генерации не может превышать, количество доступных модулей"
     * )
     * @SizeRange(propertyPath="sizeTo")
     * @Assert\Expression(
     *     "this.sizeTo || this.sizeFrom",
     *     message="Необходимо заполнить хотя бы одно значение из диапазона размеров статьи"
     * )
     *
     *
     * @var int
     */
    public $sizeFrom;

    /**
     * Конец диапазона модулей
     *
     * ToDO: После добавления функционала модулей, добавить валидацию по максимальному размеру статьи не больше
     *  количества дефолтный модулей. Пока указать не больше 3-х. Возможно стоит добавить help текст у поля формы.
     *  Заменить две проверки на Range с минимальным и максимальным значением 0-3
     *
     * @Assert\Type("integer", message="Максимальное количество модулей для генерации статьи должно быть числом")
     * @Assert\Range(
     *     min=0,
     *     max=10,
     *     minMessage="Количество модулей для генерации статьи не может быть отрицательной величиной",
     *     maxMessage="Количество модулей для генерации не может превышать, количество дjступных модулей"
     * )
     *
     * @var int
     */
    public $sizeTo;

    /**
     * Продвигаемое слово
     *
     * Продвигаемых слов может быть сколько угодно.
     *
     * @IsEmptyBoth(propertyPath="promotedWordCount")
     *
     * @var string[]
     */
    public $promotedWords;

    /**
     * Количество повторений продвигаемого слова
     *
     * ToDo: Проверять что передан массив из чисел. Если есть строка, то ошибка
     *
     *
     *
     * @var int[]
     */
    public $promotedWordCount;

    /**
     * Изображения для статьи
     *
     * @Assert\All({
     *     @Assert\Image(
     *          mimeTypes = {"image/jpeg", "image/jpg", "image/png"},
     *          mimeTypesMessage = "Загружаемые изображения должны иметь расширения jpeg, jpg или png.
     *                              Прикреплены файлы расширения {{ type }}"
     *     ),
     *     @Assert\File(
     *          maxSize = "2M",
     *          maxSizeMessage = "Изображение не должно быть размером более 2Мб. Ваш файл имеет размер {{ size }}
     *                              {{ suffix }}",
     *     )
     * })
     * @Assert\Count(
     *     max = 5,
     *     maxMessage="Возможна загрузка не более пяти изображений"
     * )
     *
     * @var array
     */
    public array $images;

    /**
     * Ссылки на изображения для статьи
     * @Assert\All(
     *     @Assert\Type(
     *     "string",
     *     message="Ссылки на изображения должны быть строкой"
     *     ),
     *     @Assert\Url(
     *     message="Url {{ value }} не валиден. Разрешены протоколы http, https, ftp",
     *     protocols={"http", "https", "ftp"}
     *     )
     * )
     *
     *
     * @Assert\Count(
     *     max = 5,
     *     maxMessage="Возможна загрузка не более пяти изображений"
     * )
     *
     * @var array
     */
    public array $imageUrls;
}
