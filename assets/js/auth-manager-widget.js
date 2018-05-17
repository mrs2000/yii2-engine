/**
 * Auth Manager Widget
 */
$(function () {

    var $widget = $('.auth-widget');
    var $listUser = $widget.find('[name="access"]');
    var $listAccess = $widget.find('[name="roles"]');
    var $input = $('#input-access-list');

    /**
     * Update hidden input
     */
    function updateInput() {
        var values = [];
        $listUser.find('option').each(function () {
            values.push(this.value);
        });
        $input.val(values.join(','));
    }

    $widget.find('.btn-add').click(function () {
        $listAccess.dblclick();
    });

    $widget.find('.btn-remove').click(function () {
        $listUser.dblclick();
    });

    /**
     * Add access
     */
    $listAccess.dblclick(function () {
        var value = $(this).val();
        if ($listUser.find('option[value="' + value + '"]').length == 0) {
            var $option = $(this).find('option[value="' + value + '"]');
            var groupName = $option.closest('optgroup').attr('label');
            var $group = $listUser.find('optgroup[label="' + groupName + '"]');
            $group.append('<option value="' + value + '">' + $option.text() + '</option>');
            updateInput();
        }
    });

    /**
     * Remove access
     */
    $listUser.dblclick(function () {
        var value = $(this).val();
        if (value) {
            $listUser.find('option[value="' + value + '"]').remove();
            updateInput();
        }
    });
});