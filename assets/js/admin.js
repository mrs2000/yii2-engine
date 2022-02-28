function create_window(callback) {
    let $wnd = $('.cp-modal');
    if ($wnd.length == 0) {
        $wnd = $('<div class="modal fade cp-modal"><div class="modal-dialog"><div class="modal-content">' +
            '<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>' +
            '<h4 class="modal-title">' + strings.admin_panel + '</h4></div>' +
            '<div class="modal-body"></div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-primary modal-yes">' + strings.yes + '</button>' +
            '<button type="button" class="btn btn-default modal-no">' + strings.no + '</button>' +
            '</div></div></div></div>');

        $wnd.find('.btn').on('click', function () {
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
    const $wnd = create_window(callback);
    $wnd.find('.modal-body').html(text);
    $wnd.find('.modal-no').hide();
}

/**
 * Диалоговое окно с вопросом
 * @param text
 * @param callback
 */
function ui_confirm(text, callback) {
    const $wnd = create_window(callback);
    $wnd.find('.modal-body').html(text);
    $wnd.find('.modal-no').show();
    $wnd.modal();
}

/**
 * Ссылка на дейсвие
 * @param action
 * @param search
 */
function get_action_url(action, search) {
    const mas = location.pathname.split('/');
    if (mas[1].length < 3) mas[4] = action; else mas[3] = action;
    const loc_serach = location.search ? location.search + '&' : '?';
    search = search ? loc_serach + search : location.search;
    let path = mas.join('/'),
        suffix = $('#url-suffix').attr('content');
    if (suffix && path.substr(path.length - 1) != suffix) {
        path += suffix;
    }
    return path + search;
}

/**
 * Изменить действие и отправить форму
 * @param action
 * @param search
 */
function change_action_and_submit(action, search) {
    const url = get_action_url(action, search);
    $('#command-form').attr('action', url).trigger('submit');
}

let processTimerID = null;

function processStart(delay) {
    function showShadow() {
        let $ps = $('#process-shadow');
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

$(function () {

    $(document).ajaxSuccess(function (e, xhr, settings) {
        if (xhr.readyState == 4 && settings.dataType == 'json') {
            const response = JSON.parse(xhr.responseText);

            //Сесcия закончилась
            if (response.nosession) {
                e.preventDefault();
                location.href = '/admin/auth/login'; //Открыть страницу логина
                return false;
            }
        }
    });

    const $commandForm = $('#command-form');

    /**
     * Запрет отправки формы по нажатию Enter
     */
    $commandForm.on('keydown', 'input', function (e) {
        if (e.which == 13) e.preventDefault();
    });

    /**
     * Командные кнопки
     */
    $('.action').on('click', function () {

        if ($(this).attr('href')) return true;

        if ($(this).attr('data-need-items') && $('.select-on-check:checked').length == 0) {
            ui_alert(strings.no_checked_items);
            return false;
        }

        const action = $(this).attr('data-action');
        if (action) {
            const search = $(this).attr('data-search');
            const confirm_message = $(this).attr('data-confirm');
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
        const id = $(this).attr('data-id');
        selectItem(id);
        change_action_and_submit('position');
    });

    /**
     * Смена статуса записи
     */
    $commandForm.on('change', '.state', function () {
        const $obj = $(this);
        selectItem($obj.attr('data-id'));
        change_action_and_submit($obj.attr('data-action'), 'attribute=' + $obj.attr('name') + '&value=' + $obj.val());
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