<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Krasimir Berov 2010 
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage 
 * @license    LGPL3 
 * @filesource
 */


/**
 * Table tl_article 
 *
 */
$this->loadLanguageFile('languages');
$GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'] = array('tl_article_l10ns','addIcon');
class tl_article_l10ns extends tl_article 
{
    /**
     * Add summary of elements by language.
     * TODO: Make it possible to switch summary off. add widget to tl_settings.
     */
    public function addIcon($row, $label) {
        $label = parent::addIcon($row, $label);
        //count content elements in different languages and display them
        $sql='SELECT COUNT(id) items,language FROM tl_content WHERE pid =? GROUP BY language';
        $items = $this->Database->prepare($sql)->execute($row['id'])->fetchAllAssoc();
        if(!empty($items)) foreach($items as $lang) {
            $count = $lang['items'];
            $title = $GLOBALS['TL_LANG']['LNG'][$lang['language']].": $count elements";
            $label .= ($lang['language']?
		         '<img title="'.$title.'" style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/flag_icons/png/'
                .$lang['language'].'.png" /> ':'
                <img title="'.$title.'" style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/icon.png" />');
        }
        return $label;
    }

}
?>