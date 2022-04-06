# Сайт по генерации контента статьи #

## Деплой проекта ##

В файле /etc/hosts добавить запись 127.0.0.1 blablaarticle.

Собрать и запустить проект командой:
```shell
docker-compose up -d --build
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

По умолчанию новому пользователю присваивается подписка FREE. Так же происходит генерация 
API тоекна.

## Авторизация

Происходит через форму по паролю и электронной почте. Так же осуществляется проверка 
подтверждения почты пользователем. Так же существует авторизация по API токену для доступа 
к функционалу генерации статей по API.

Все административные разделы доступны лишь пользователям с подтвержденной почтой. Для 
проверки используется класс Voter, он действует на все методы контроллеров административного 
раздела.

## Профиль

Профиль пользователя доступен только подтвердившим свою почту пользователям. На странице
есть форма для изменения пользовательской информации. Так же есть форма для генерации нового 
токена. Запрос на обновление токена отправляется посредством ajax, далее javascript отображает
сообщени об успешном обновлении, либо производит вывод сообщения об ошибке.

## Подписки

Подписки хранятся в БД в таблице subscription. В таблице хранятся названия подписок,
их стоимость в долларах и опции. Информация о сроке истечения подписки пользователя хранится
в поле expire_at таблицы user. При истечении срока подписки она понижается на
уровень FREE. За понижение подписки отвечает консольная команда
DowngradeExpiredSubscriptionCommand.php, которую cron должен запускать ежедневно.

## Модули для генерации статей

Страница модулей доступна только пользователям с подпиской уровня PRO. За это отвечает класс Voter. 
На данной странице отображается список существующих модулей пользователя и форма для их создания.

Список модулей выводится по убыванию с даты создания модуля. Максимальное количество модулей для 
отображения - 10 шт. В крайней правой колонке существует кнопка для удаления модуля.

Форма для добавления модулей выводится сразу под их списком. Для создания модуля необходимо 
заполнить поля "Название модуля" и "Код модуля". В качестве кода модуля необходимо использовать 
html теги и функционал шаблонов twig. 

Существуют следующие плейсхолдеры, использование которых допустимо в модулях:
- {{ keyword }} - для вставки ключевого слова
- {{ keyword|morph(number) }} - для вставки ключевого слова в определенной словоформе
- {{ title }} - для вставки заголовка модуля (подзаголовки)
- {{ paragraph }} - для вставки текста одного абзаца (без тега \<p>)
- {{ paragraphs }} - для вставки случайного количества параграфов от 1 до 3, при этом 
теги \<p> устанавливаются автоматически
- {{ imageSrc }} - путь к картинке, предполагается использовать внутри тегов \<img>

Для плейсхолдера keyword так же существуют 7 словоформ, которые представляют собой форму ключевого 
слова в определенном падеже (с 0 до 5 - от Именительного до Предложного) или его множественную форму (6).
Указываются в разметке они через фильтр twig следующим образом:
```html
{{ keyword|morph(2) }}
```

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
