jQuery(function ($) {
    $('#add_new_custom_strategy').on('click', function (e) {
        e.preventDefault();
        var row = $(this).closest('tr'),
            newrowcount = $('.cache-strategies-table tbody tr').length,
            rowHtml = '<tr>\n' +
                '                                                <td>\n' +
                '                                                    <span class="url-path"><input  name="custom_strategies['+newrowcount+'][path]" type="text" size="15" class="regular-text code"></span>\n' +
                '                                                </td>\n' +
                '                                                <td>\n' +
                '                                                    <span class="cache-strategy">\n' +
                '                                                        <select name="custom_strategies['+newrowcount+'][strategy]">\n' +
                '                                                            <option class="level-0" value="cacheFirst">Cache First</option>\n' +
                '                                                            <option class="level-0" value="networkFirst">Network First</option>\n' +
                '                                                            <option class="level-0" value="networkOnly">Network Only</option>\n' +
                '                                                        </select>\n' +
                '                                                    </span>\n' +
                '                                                </td>\n' +
                '                                                <td align="center">\n' +
                '                                                    <span class="remove-icon">\n' +
                '                                                        <button class="remove_custom_strategy offline-pre-button"><i class="dashicons dashicons-trash"></i> </button>\n' +
                '                                                    </span>\n' +
                '                                                </td>\n' +
                '                                            </tr>';

        $(rowHtml).insertBefore(row);
    });
    $('.cache-strategies-table').on('click', '.remove_custom_strategy', function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });
});