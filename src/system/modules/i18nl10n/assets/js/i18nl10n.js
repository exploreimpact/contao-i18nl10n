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

        var image = $(el).getFirst('img'),
            publish = (image.src.indexOf('invisible') != -1),
            div = el.getParent('div');

        // Send the request
        if (publish) {
            // set visible
            image.src = image.src.replace('invisible.gif', 'visible.gif');

            new Request.Contao().post({
                action: 'toggleL10n',
                id: id, 
                state: 0, 
                table: 'tl_page_i18nl10n',
                REQUEST_TOKEN: Contao.request_token
            });
        } else {
            // set invisible
            image.src = image.src.replace('visible.gif', 'invisible.gif');

            new Request.Contao().post({
                action: 'toggleL10n',
                id: id,
                state: 1,
                table: 'tl_page_i18nl10n',
                REQUEST_TOKEN: Contao.request_token
            });
        }

        return false;
    },

    toggleFunctions: function() {
        var containers = $$('.i18nl10n_languages');
        containers.toggleClass('open');
    }
};