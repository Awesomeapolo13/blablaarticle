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

Затем перейти на blablaarticle:8080