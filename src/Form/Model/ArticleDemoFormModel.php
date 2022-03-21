<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Объект DTO для формы демонстрационной генерации статьи
 */
class ArticleDemoFormModel
{
    /**
     * Заголовок статьи
     *
     * @Assert\NotBlank(message="Введите заголовок")
     */
    public $title;

    /**
     * Продвигаемое в статье слово
     *
     * @Assert\NotBlank(message="Введите продвигаемое слово")
     */
    public $promotedWord;
}
