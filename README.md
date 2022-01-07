# Сайт по генерации контента статьи #

## Деплой проекта ##

В файле /etc/hosts добавить запись 127.0.0.1 blablaarticle.

Собрать проект командой:

```shell
docker-compose build
```

Запустить проект командой:
```shell
docker-compose up -d
```
Установить зависимости проекта:

```shell
docker-compose run --rm php-8.0 composer install
```

Произвести установку зависимостей yarn командой:
```shell
docker-compose run --rm node yarn install
```

Сделать сборку скриптов и стилей проекта. Для этого можно использовать одну из ниже указанных команд:
```shell
docker-compose run --rm node yarn dev
```
либо
```shell
docker-compose run --rm node yarn watch
```

Создать базу данных:
```shell
docker-compose run --rm php-8.0 php bin/console doctrine:database:create
```

Загрузить миграции:
```shell
docker-compose run --rm php-8.0 php bin/console doctrine:migrations:migrate
```

Загрузить фикстуры:
```shell
docker-compose run --rm php-8.0 php bin/console doctrine:fixtures:load
```

Затем перейти на blablaarticle:8080

## Регистрация ##

Регистрация выполнена через форму. Чтобы зарегистрироваться, необходимо ввести 
имя, email, пароль и подтвердить его. После заполнения формы на электронную почту 
придет ссылка. Для завершения регистрации необходимо подтвердить email, перейдя 
по этой ссылке.

## Генерация статей ##

### Общее описание процесса ###

Генерация статей базируется на паттерне Стратегия. Существует класс контекста - ArticleGenerator,
который может генерировать статьи в соответствии с переданной стратегией и объектом модели формы.
Результат генерации - строка, содержащая весь контент статьи (html-разметку, текст статьи и пр.). 
Работа генерации в зависимости от переданной стратегии описана ниже.

### Демонстрационная генерация статьи ###

Реализована классом стратегии DemoArticleGenerationStrategy. Класс DTO для реализации содержит
продвигаемое слово и заголовок статьи, прочие параметры заданы по умолчанию. 
Генерация статей для демонстрации доступна любому неавторизованному пользователю.
Сгенерировать статью можно только один раз, после этого id этой статьи записывается в cookie
и форма блокируется. 

