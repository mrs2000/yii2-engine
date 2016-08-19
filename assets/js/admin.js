function create_window(callback) {
    var $wnd = $('.cp-modal');
    if ($wnd.length == 0) {
        $wnd = $('<div class="modal fade cp-modal"><div class="modal-dialog"><div class="modal-content">' +
        '<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>' +
        '<h4 class="modal-title">' + strings.admin_panel + '</h4></div>' +
        '<div class="modal-body"></div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-primary modal-yes">' + strings.yes + '</button>' +
        '<button type="button" class="btn btn-default modal-no">' + strings.no + '</button>' +
        '</div></div></div></div>');

        $wnd.find('.btn').click(function () {
            $wnd.modal('toggle');
            callback($(this).hasClass('modal-yes'));
            return false;
        });

        $wnd.modal();
    } else {
        $wnd.modal('toggle');
    }

    return $wnd;
}

/**
 * Модальное окно с сообщением
 * @param text
 * @param callback
 */
function ui_alert(text, callback) {
    var $wnd = create_window(callback);
    $wnd.find('.modal-body').html(text);
    $wnd.find('.modal-no').hide();
}

/**
 * Диалоговое окно с вопросом
 * @param text
 * @param callback
 */
function ui_confirm(text, callback) {
    var $wnd = create_window(callback);
    $wnd.find('.modal-body').html(text);
    $wnd.find('.modal-no').show();
    $wnd.modal();
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

var processTimerID = null;
function processStart(delay) {
    function showShadow() {
        var $ps = $('#process-shadow');
        if ($ps.length == 0) {
            $ps = $('<div id="process-shadow">');
            $('body').append($ps);
        }
        $ps.show();
    }
    if (delay) {
        processTimerID = setTimeout(showShadow, delay);
    } else {
        showShadow();
    }
}

function processStop() {
    clearTimeout(processTimerID);
    $('#process-shadow').hide();
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

        if ($(this).attr('href')) return true;

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
        change_action_and_submit(obj.attr('data-action'), 'attribute=' + obj.attr('name') + '&value=' + obj.val());
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