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
     * Идентификатор статьи
     * Используется, если генерим статью на основе уже ранее созданной
     * @var int|null
     */
    public ?int $id = null;

    /**
     * Тематика
     *
     * @Assert\NotBlank(message="Укажите тематику статьи")
     * @var string
     */
    public string $theme;

    /**
     * Заголовок статьи
     * @var string|null
     */
    public ?string $title;

    /**
     * Ключевое слово
     *
     * Обязательной является форма именительного падежа (первое поле ввода).
     * Остальные словоформы указываются по желанию
     *
     * @Assert\NotBlank(message="Введите ключевое слово")
     * @var string[]
     */
    public array $articleWords;

    /**
     * Краткое описание статьи
     *
     * @Assert\Length(
     *     min=0,
     *     max=255,
     *     maxMessage="Описание должно быть кратким и не превышать 255 символов")
     * @var string|null
     */
    public ?string $description;

    /**
     * Начало диапазона модулей
     *
     * @Assert\Type("integer", message="Минимальное количество модулей для генерации статьи должно быть числом")
     * @SizeRange(propertyPath="sizeTo")
     * @Assert\Expression(
     *     "this.sizeTo || this.sizeFrom",
     *     message="Необходимо заполнить хотя бы одно значение из диапазона размеров статьи"
     * )
     *
     * @var int|null
     */
    public ?int $sizeFrom = null;

    /**
     * Конец диапазона модулей
     *
     * @Assert\Type("integer", message="Максимальное количество модулей для генерации статьи должно быть числом")
     *
     * @var int|null
     */
    public ?int $sizeTo = null;

    /**
     * Продвигаемое слово
     *
     * Продвигаемых слов может быть сколько угодно.
     *
     * @IsEmptyBoth(propertyPath="promotedWordCount")
     * @Assert\All(
     *     @Assert\Type(
     *         "string",
     *         message="Продвигаемое слово должно быть строкой"
     *     )
     * )
     *
     * @var string[]
     */
    public ?array $promotedWords;

    /**
     * Количество повторений продвигаемого слова
     *
     * @Assert\All(
     *     @Assert\Type(
     *         "integer",
     *         message="Количество продвигаемого слова должно быть числом"
     *     )
     * )
     * @var int[]
     */
    public ?array $promotedWordCount;

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
     * @Assert\Count(
     *     max = 5,
     *     maxMessage="Возможна загрузка не более пяти изображений"
     * )
     *
     * @var array
     */
    public array $imageUrls;
}
