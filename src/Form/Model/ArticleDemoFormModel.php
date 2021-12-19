<?php

namespace App\Form\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleDemoFormModel
{
    /**
     * Заголовок статьи
     *
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Введите заголовок")
     */
    public $title;

    /**
     * Продвигаемое в статье слово
     *
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Введите продвигаемое слово")
     */
    public $promotedWord;
}
