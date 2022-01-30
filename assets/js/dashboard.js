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

})(jQuery);
