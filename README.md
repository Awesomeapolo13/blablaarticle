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