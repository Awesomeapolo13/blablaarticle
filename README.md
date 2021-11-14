# Сайт по генерации контента статьи #

## Запуск проекта ##

В файле /etc/hosts добавить запись 127.0.0.1 blablaarticle.
Установить все необходимые зависимости:

```
composer install
```

Собрать проект командой:

```
docker-compose build
```

Запустить проект командой:
```
docker-compose up -d
```

Произвести установку зависимостей yarn командой:
```
docker-compose run --rm node yarn install
```

Сделать сборку скриптов и стилей проекта. Для этого можно использовать одну из ниже указанных команд:
```
docker-compose run --rm node yarn dev
```
либо
```
docker-compose run --rm node yarn watch
```

Затем перейти на blablaarticle:8080