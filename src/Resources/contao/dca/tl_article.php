<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n dca
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Class tl_article_l10n
 */
class tl_article_l10n extends tl_article
{
    /**
     * Label callback for list view
     *
     * Append language flags to article entries
     *
     * @param      $arrArgs
     * @param null $arrVendorCallback
     *
     * @return string
     */
    public function labelCallback($arrArgs, $arrVendorCallback = null)
    {
        $strElement = '';

        // Callback should always be available, since it's part of the basic DCA
        if (is_array($arrVendorCallback)) {
            $vendorClass = new $arrVendorCallback[0];
            $strElement = call_user_func_array(array($vendorClass, $arrVendorCallback[1]), $arrArgs);
        } elseif (is_callable($arrVendorCallback)) {
            $strElement = call_user_func_array($arrVendorCallback, $arrArgs);
        }

        return $this->addIcon($arrArgs[0], $strElement);
    }

    /**
     * Add summary of elements by language as tooltip
     *
     * @param   Array   $row
     * @param   String  $label
     *
     * @return string
     */
    public function addIcon($row, $label)
    {

        $sql   = 'SELECT COUNT(id) items, language FROM tl_content WHERE pid =? GROUP BY language';

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
                $count          = $l10nItem['items'];
                $title          = $GLOBALS['TL_LANG']['LNG'][$l10nItem['language']] . ": $count " . $GLOBALS['TL_LANG']['tl_article']['elements'];
                $l10nItemIcon   = 'bundles/verstaerkeri18nl10n/img/i18nl10n.png';

                if ($l10nItem['language'])
                {
                    $l10nItemIcon = 'bundles/verstaerkeri18nl10n/img/flag_icons/' . $l10nItem['language'] . '.png';
                }

                $label .= '<img class="i18nl10n_article_flag" title="' . $title . '" src="' . $l10nItemIcon . '" />';
            }
        }

        return $label;
    }
}
