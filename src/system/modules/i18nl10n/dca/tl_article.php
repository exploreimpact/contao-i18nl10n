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
     * Add summary of elements by language as tooltip.
     */
    public function addIcon($row, $label)
    {
        $label = parent::addIcon($row, $label);
        $sql = 'SELECT COUNT(id) items, language FROM tl_content WHERE pid =? GROUP BY language';

        // count content elements in different languages and display them
        $items = \Database::getInstance()
            ->prepare($sql)
            ->execute($row['id'])
            ->fetchAllAssoc();

        // build icon elements
        if (!empty($items))
        {
            foreach ($items as $l10nItem)
            {
                $count = $l10nItem['items'];
                $title = $GLOBALS['TL_LANG']['LNG'][$l10nItem['language']] . ": $count " . $GLOBALS['TL_LANG']['tl_article']['elements'];
                $l10nItemIcon = 'system/modules/i18nl10n/assets/img/i18nl10n.png';

                if ($l10nItem['language'])
                {
                    $l10nItemIcon = 'system/modules/i18nl10n/assets/img/flag_icons/' . $l10nItem['language'] . '.png';
                }

                $label .= '<img class="i18nl10n_article_flag" title="' . $title . '" src="' . $l10nItemIcon . '" />';
            }
        }

        return $label;
    }

}