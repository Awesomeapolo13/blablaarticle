<?php

namespace App\DataFixtures;

use App\Entity\Module;
use Doctrine\Persistence\ObjectManager;

/**
 * Класс фикстур модулей для генерации статей
 */
class ModuleFixtures extends BaseFixtures
{
    /**
     * Дефолтные модули, используемые приложением по умолчанию
     *
     * @var \string[][]
     */
    private $defaultModules = [
        [
            'name' => 'Медиа блок - картинка и параграфы',
            'body' => '<div class="media">
    <img class="mr-3" src="https://via.placeholder.com/250x250" width="250" height="250" alt="">
    <div class="media-body">
        {{ paragraphs }}
    </div>
</div>',
        ],
        [
            'name' => 'Медиа слева - параграфы потом картинка',
            'body' => '<div class="media">
    <div class="media-body">
        {{ paragraphs }}
    </div>
    <img class="ml-3" src="https://via.placeholder.com/250x250" width="250" height="250" alt="">
</div>',
        ],
        [
            'name' => 'Медиа слева - один параграф и картинка',
            'body' => '<div class="media">
    <div class="media-body">
        <p>{{ paragraph }}</p>
    </div>
    <img class="ml-3" src="https://via.placeholder.com/250x250" width="250" height="250" alt="">
</div>',
        ],
        [
            'name' => 'Медиа справа - картинка потом один параграф',
            'body' => '<div class="media">
    <img class="mr-3" src="https://via.placeholder.com/250x250" width="250" height="250" alt="">
    <div class="media-body">
        <p>{{ paragraph }}</p>
    </div>
</div>',
        ],
        [
            'name' => 'Контент с подзаголовком - подзаголовок и параграф',
            'body' => '<h3>{{ title }}</h3>
<p>{{ paragraph }}</p>'
        ],
        [
            'name' => 'Параграфы',
            'body' => '{{ paragraphs }}',
        ],
        [
            'name' => 'Заголовок с контентом - большой заголовок и параграфы',
            'body' => '<h1>{{ title }}</h1>
<p>{{ paragraph }}</p>',
        ],
        [
            'name' => 'Текст в две колонки',
            'body' => '<div class="row">
    <div class="col-sm-6">
        {{ paragraphs }}      
    </div>
    <div class="col-sm-6">
        {{ paragraphs }}
    </div>
</div>',
        ],
    ];

    /**
     * Загружает в БД набор дефолтных модулей
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function loadData(ObjectManager $manager)
    {
        foreach ($this->defaultModules as $key => $defaultModule) {
            $entity = $this->create(Module::class, function (Module $module) use ($manager, $defaultModule) {
                $module
                    ->setName($defaultModule['name'])
                    ->setBody($defaultModule['body'])
                ;
            });

            $this->addReference(Module::class . "|$key", $entity);
        }

        $manager->flush();
    }
}
