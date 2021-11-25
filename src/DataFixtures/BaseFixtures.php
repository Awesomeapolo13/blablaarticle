<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Базовый класс фикстур
 *
 * Предоставляет интерфейс для создания множества объектов,
 * а так же сервис faker
 */
abstract class BaseFixtures extends Fixture
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var ObjectManager
     */
    protected $manager;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->manager = $manager;

        $this->loadData($manager);
    }

    /**
     * Создает и подготавливает объекты для загрузки в БД
     *
     * @param ObjectManager $manager
     * @return mixed
     */
    abstract public function loadData(ObjectManager $manager);

    /**
     * Создает один экземпляр класса модели className
     *
     * @param string $className
     * @param callable $factory
     * @return mixed
     */
    protected function create(string $className, callable $factory)
    {
        $entity = new $className();
        $factory($entity);
        $this->manager->persist($entity);

        return $entity;
    }

    /**
     * Создает указанное количество модели classname, согласно переданной коллбек функции
     *
     * @param string $className - имя класса модели
     * @param int $modelCount - количество моделей, которое необходимо создать
     * @param callable $factory - коллбек функция в соответствии с кодом которой будут создаваться объекты
     */
    protected function createMany(string $className, int $modelCount, callable $factory): void
    {
        for($i = 0; $i < $modelCount; $i++) {
            $this->create($className, $factory);
        }

        $this->manager->flush();
    }
}
