<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Объект DTO для формы создания нового модуля
 */
class ModuleFormModel
{
    /**
     * Название модуля
     *
     * @Assert\NotBlank(message="Введите название модуля")
     */
    public $name;

    /**
     * Тело модуля (его разметка)
     *
     * @Assert\NotBlank(message="Введите разметку модуля")
     */
    public $body;
}
