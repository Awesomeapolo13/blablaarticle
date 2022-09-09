<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
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

    /**
     * @var array - массив ссылок на уже созданные элементы
     */
    private $referencesIndex =[];

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
            $entity = $this->create($className, $factory);

            /**
             * В качестве первого параметра принимает id объекта (по нему его получают из системы), второй - его сущность.
             * Теперь можно связывать сущности фикстур
             */
            $this->addReference("$className|$i", $entity);
        }

        $this->manager->flush();
    }

    /**
     * Возвращает рандомный объект переданного класса
     *
     * @param $className
     * @return object
     * @throws Exception
     */
    protected function getRandomReference($className): object
    {
        // Если данные внутри класса уже есть, если нет то заходит внутрь
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];
            // выбираем все связи из объекта referenceRepository
            foreach ($this->referenceRepository->getReferences() as $key => $reference) {
                // проверяем содержит ли ключ этой связи имя класса
                if (strpos($key, $className . '|') === 0) {
                    // если да то собираем массив из элементов, указывающих на нужный класс
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
        // если referencesIndex пустой, то исключение
        if (empty($this->referencesIndex[$className])) {
            throw new Exception('Не найдены ссылки на класс ' . $className);
        }

        return $this->getReference($this->faker->randomElement($this->referencesIndex[$className]));
    }
}
