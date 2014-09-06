<?php

/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * PHP version 5
 * @copyright   Verstärker, Patric Eberle 2014
 * @copyright   Krasimir Berov 2010-2013
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Krasimir Berov
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Table tl_article
 */
$GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'] = array
(
    'tl_article_l10n',
    'addIcon'
);


class tl_article_l10n extends tl_article
{
    /**
     * Add summary of elements by language.
     * TODO: Make it possible to switch summary off. add widget to tl_settings.
     */
    public function addIcon($row, $label) {
        $label = parent::addIcon($row, $label);
        $sql = "
          SELECT
            COUNT(id) items, language
          FROM
            tl_content
          WHERE
            pid =?
          GROUP BY
            language
        ";

        // count content elements in different languages and display them
        $items = \Database::getInstance()
            ->prepare($sql)
            ->execute($row['id'])
            ->fetchAllAssoc();

        // build icon elements
        if(!empty($items)) {
            foreach($items as $lang) {
                $count = $lang['items'];
                $title = $GLOBALS['TL_LANG']['LNG'][$lang['language']] . ": $count " . $GLOBALS['TL_LANG']['tl_article']['elements'];
                $langIcon = 'system/modules/core_i18nl10n/assets/img/i18nl10n.png';

                if($lang['language']) {
                    $langIcon = 'system/modules/core_i18nl10n/assets/img/flag_icons/' . $lang['language'] . '.png';
                }

                $label .= '<img class="i18nl10n_article_flag" title="' . $title . '" src="' . $langIcon . '" />';
            }
        }

        return $label;
    }

}