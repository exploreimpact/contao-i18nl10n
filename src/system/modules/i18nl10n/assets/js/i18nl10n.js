/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   Verstärker, Patric Eberle 2014
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n Ajax
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

var I18nl10n =
{
    /**
     * Toggle the visibility of an l1on translation
     *
     * @param {object} el    The DOM element
     * @param {string} id    The ID of the target element
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