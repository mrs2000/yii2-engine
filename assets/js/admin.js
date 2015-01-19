/**
 * Модальное окно с сообщением
 * @param text
 */
function ui_alert(text) {

    alert(text); return;


    $('<div title="' + strings.admin_panel +'">' + text + '</div>').dialog({
        resizable: false,
        height:'auto',
        width: 400,
        modal: true,
        buttons: {
            'Закрыть': function() {
                $( this ).dialog('close');
            }
        }
    });
}

/**
 * Диалоговое окно с вопросом
 * @param text
 * @param callback
 */
function ui_confirm(text, callback) {

    callback(confirm(text)); return;

    $('<div title="' + strings.admin_panel +'">' + text + '</div>').dialog({
        resizable: false,
        height:'auto',
        width: 400,
        modal: true,
        buttons: {
            'Да': function() {
                callback(true);
                $(this).dialog('close');
            },
            'Нет': function() {
                callback(false);
                $(this).dialog('close');
            }
        }
    });
}

/**
 * Изменить действие и отправить форму
 * @param action
 * @param search
 */
function change_action_and_submit(action, search) {
    var mas = location.pathname.split('/');
    if (mas[1].length < 3) mas[4] = action; else mas[3] = action;
    var loc_serach = location.search ? location.search + '&' : '?';
    search = search ? loc_serach + search : location.search;
    $('#command-form')
        .attr('action', mas.join('/') + search)
        .submit();
}

$(document).ready(function() {

    $(document).ajaxSuccess(function (e, xhr, settings) {
        if (xhr.readyState == 4 && settings.dataType == 'json') {
            var response = $.parseJSON(xhr.responseText);

            //Сесcия закончилась
            if (response.nosession) {
                e.preventDefault();
                location.href = '/admin/auth/login'; //Открыть страницу логина
                return false;
            }
        }
    });

    var $commandForm = $('#command-form');

    /**
     * Запрет отправки формы по нажатию Enter
     */
    $commandForm.on('keydown', 'input', function (e) {
        if (e.which == 13) e.preventDefault();
    });

    /**
     * Командные кнопки
     */
    $('.action').click(function() {

        if ($(this).attr('href') != '#') return true;

        if ($(this).attr('data-need-items') && $('.select-on-check:checked').length == 0) {
            ui_alert(strings.no_checked_items);
            return false;
        }

        var action = $(this).attr('data-action');
        if (action) {
            var search = $(this).attr('data-search');
            var confirm_message = $(this).attr('data-confirm');
            if (confirm_message) {
                ui_confirm(confirm_message, function (result) {
                    if (result) change_action_and_submit(action, search);
                });
                return false;
            }
            change_action_and_submit(action, search);
        }
        return false;
    });

    /**
     * Смена позиции
     */
    $commandForm.on('change', '.position', function () {
        var id = $(this).attr('data-id');
        selectItem(id);
        change_action_and_submit('position');
    });

    /**
     * Смена статуса записи
     */
    $commandForm.on('change', '.state', function () {
        var obj = $(this);
        selectItem(obj.attr('data-id'));
        change_action_and_submit('state', 'attribute=' + obj.attr('name') + '&value=' + obj.val());
        return false;
    });

    function selectItem(id) {
        $('.select-on-check').prop('checked', false);
        $('.select-on-check[value="' + id + '"]').prop('checked', true);
    }

    $(document).on('pjax:success', function () {
        $('input[name="urlParams"]').val(location.search.substr(1));
    });
});