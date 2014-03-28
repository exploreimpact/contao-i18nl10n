<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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