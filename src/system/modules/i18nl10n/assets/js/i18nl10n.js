/**
 * Created by atreju on 13.06.14.
 */

var I18nl10n =
{
    /**
     * Toggle the visibility of an l1on translation
     *
     * @param {object} el    The DOM element
     * @param {string} id    The ID of the target element
     * @param {string} table The table name
     *
     * @returns {boolean}
     */
    toggleL10n: function (el, id) {

        el.blur();

        var icon = $(el).getFirst('img'),
            flag = $(el).getParent('li').getElement('.i18nl10n_flag'),
            publish = (icon.src.indexOf('invisible') != -1),
            div = el.getParent('div');

        // Send the request
        if (publish) {
            // set visible
            icon.src = icon.src.replace('invisible.gif', 'visible.gif');
            flag.src = flag.src.replace(/_invisible\.(gif|png|jpe?g)/, '.$1');

            new Request.Contao().post({
                action: 'toggleL10n',
                id: id,
                state: 0,
                table: 'tl_page_i18nl10n',
                REQUEST_TOKEN: Contao.request_token
            });
        } else {
            // set invisible
            icon.src = icon.src.replace('visible.gif', 'invisible.gif');
            flag.src = flag.src.replace(/\.(gif|png|jpe?g)/, '_invisible.$1');

            new Request.Contao().post({
                action: 'toggleL10n',
                id: id,
                state: 1,
                REQUEST_TOKEN: Contao.request_token
            });
        }

        return false;
    }

};