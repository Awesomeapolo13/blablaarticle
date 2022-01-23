const jQuery = require('jquery');

(function ($) {
    "use strict";

    $("#menu-toggle").on('click', function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    // Обработчик кнопки добавления продвигаемого слова
    $(".add-promoted-word").on('click', function (e) {
        e.preventDefault();
        let list = $($(this).attr('data-list-selector'));

        for (let key = 0; key <= 1; key++) {
            const elem = list[key];

            let counter = (elem.childNodes.length) - 2;

            let newWidget = elem.getAttribute('data-prototype');
            newWidget = newWidget.replace(/__name__/g, counter);

            counter++;

            let newElem = $(elem.getAttribute('data-widget-tags')).html(newWidget);
            newElem.appendTo(elem);
        }

    })

})(jQuery);
