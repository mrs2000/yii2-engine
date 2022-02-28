/**
 * Auth Manager Widget
 */
$(function () {

    const $widget = $('.auth-widget');
    const $listUser = $widget.find('[name="access"]');
    const $listAccess = $widget.find('[name="roles"]');
    const $input = $('#input-access-list');

    /**
     * Update hidden input
     */
    function updateInput() {
        const values = [];
        $listUser.find('option').each(function () {
            values.push(this.value);
        });
        $input.val(values.join(','));
    }

    $widget.find('.btn-add').on('click',function () {
        $listAccess.trigger('dblclick');
    });

    $widget.find('.btn-remove').on('click',function () {
        $listUser.trigger('dblclick');
    });

    /**
     * Add access
     */
    $listAccess.on('dblclick',function () {
        const value = $(this).val();
        if ($listUser.find('option[value="' + value + '"]').length == 0) {
            const $option = $(this).find('option[value="' + value + '"]');
            const groupName = $option.closest('optgroup').attr('label');
            const $group = $listUser.find('optgroup[label="' + groupName + '"]');
            $group.append('<option value="' + value + '">' + $option.text() + '</option>');
            updateInput();
        }
    });

    /**
     * Remove access
     */
    $listUser.on('dblclick',function () {
        const value = $(this).val();
        if (value) {
            $listUser.find('option[value="' + value + '"]').remove();
            updateInput();
        }
    });
});