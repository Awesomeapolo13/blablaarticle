const jQuery = require('jquery');

(function ($) {
    "use strict";

    $("#menu-toggle").on('click', function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    // Кнопка добавления полей продвигаемого слова
    const addPromotedWordButton = $('.add-promoted-word');
    // Кнопка удаления полей продвигаемого слова
    const removePromotedWordButton = $(".remove-promoted-word");
    // Блок с полями продвигаемого слова
    const list = $(addPromotedWordButton.attr('data-list-selector'));

    hideOrShowRemoveFieldButton(list, removePromotedWordButton);

    /**
     * Обработчик кнопки добавления полей для продвигаемого слова
     */
    addPromotedWordButton.on('click', function (e) {
        e.preventDefault();
        const list = $($(this).attr('data-list-selector'));

        for (let key = 0; key <= 1; key++) {
            const elem = list[key];

            let counter = (elem.childNodes.length) - 2;

            let newWidget = elem.getAttribute('data-prototype');
            newWidget = newWidget.replace(/__name__/g, counter);

            counter++;

            let newElem = $(elem.getAttribute('data-widget-tags')).html(newWidget);
            newElem.appendTo(elem);

            hideOrShowRemoveFieldButton(list, removePromotedWordButton);
        }

    });

    /**
     * Обработчик кнопки удаления полей для продвигаемого слова
     */
    removePromotedWordButton.on('click', function (e) {
        e.preventDefault();
        const list = $($(this).attr('data-list-selector'));

        for (let key = 0; key <= 1; key++) {
            const elem = list[key];
            const lastElement = elem.childNodes[(elem.childNodes.length) - 1];
            lastElement.remove();
            hideOrShowRemoveFieldButton(list, removePromotedWordButton)
        }
    })

    /**
     * Скрывает или отображает кнопку удаления полей продвигаемого слова
     *
     * @param fieldsList - блок с полями продвигаемого слова, выбранный JQuery селектором
     * @param button - кнопка выбранная с помощью JQuery селектора
     */
    function hideOrShowRemoveFieldButton(fieldsList, button) {
        fieldsList.children().length <= 2 ? button.hide() : button.show();
    }

    /**
     * Скрывает элемент через промежуток времени.
     * При передаче третьего параметра, элемент скрывается постеменно
     *
     * @param element - элемент, который необходимо скрыть
     * @param timeout - промежуток времени в мс, через который элемент будет скрыт
     * @param hiddenTime - время за которое элемент полностью исчезнет со страницы
     */
    function hideElementAfterTimeout(element, timeout, hiddenTime = 0) {
        setTimeout(function (element) {
            element.hide(hiddenTime);
        }, timeout, element);
    }

    /**
     * Обработчик кнопки генерации нового api токена
     */
    $('.update-api-token').on('click', function (e) {
        e.preventDefault();
        const apiTokenElem = $('.api-token');
        let apiToken = apiTokenElem.val();
        let messageBlock = $('.token-expired');
        // аякс запрос на получение
        $.ajax({
            headers: {
                "Authorization": apiToken
            },
            url: '/admin/update_api_token',
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: 'json',
            // В случае успеха, отображает новы  токен, выводит сообщение об успешном изменении токена
            success: function (response) {
                apiTokenElem.text(response.token);
                if (!messageBlock.length) {
                    const contentCol = $('.cont-block');
                    contentCol.prepend("<div class='alert token-expired'></div>");
                    messageBlock = $('.token-expired');
                }
                messageBlock.removeClass('alert-warning');
                messageBlock.addClass('alert-success');
                messageBlock.text('Токен успешно изменен.');
                // Убирает сообщение об успехе через 10 секунд
                hideElementAfterTimeout(messageBlock, 10000, 5000);
            },
            // В случае ошибки показывает пользователю сообщени об ошибке
            error: function () {
                if (!messageBlock.length) {
                    const contentCol = $('.cont-block');
                    contentCol.prepend("<div class='alert token-expired'></div>");
                    messageBlock = $('.token-expired');
                }
                messageBlock.removeClass('alert-warning');
                messageBlock.addClass('alert-error');
                messageBlock.text('Ощибка при изменении токена. Попробуйте позднее.');
                // Убирает сообщение об успехе через 10 секунд
                hideElementAfterTimeout(messageBlock,10000, 5000);
            }
        });

    });

})(jQuery);
