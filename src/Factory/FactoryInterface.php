<?php

namespace App\Factory;

/**
 * Общий интерфейс для создания объектов
 */
interface FactoryInterface
{
    /**
     * @param object $model - DTO или другой объект на основе которого будут создаваться объекты
     * @return object - результирующий объект
     */
    public function createFromModel(object $model): object;
}
