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
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['page_i18nl10n'] = array(
    'label'               => 'L10Ns',
    'href'                => 'do=i18nl10n',
    'button_callback'     => array('tl_page_l10ns', 'editl10ns')
);
$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = 
    array('tl_page_l10ns', 'setDefaultLanguage');

class tl_page_l10ns extends Backend {
    public function editl10ns($row, $href, $label, $title, $icon) {
        $button ='';
        //TODO: think about a new page type: regular_localized
        if($row['type'] == 'regular'){
            $title = sprintf($GLOBALS['TL_LANG']['MSC']['editl10ns'],"\"{$row['title']}\"");
            $button .='<a href="' . $this->addToUrl($href.'&amp;node='.$row['id']) . '" title="'.specialchars($title).'">'
            .'<img src="system/modules/i18nl10n/html/icon.png" /></a> ';
        }
        return $button;
    }
    /**
	 * Apply the root page language to new pages
	 */
	public function setDefaultLanguage()
	{
		if ($this->Input->get('act') != 'create')
		{
			return;
		}

		if ($this->Input->get('pid') == 0)
		{
			$GLOBALS['TL_DCA']['tl_page']['fields']['language']['default'] = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
		}
		else
		{
			$objPage = $this->getPageDetails($this->Input->get('pid'));
			$GLOBALS['TL_DCA']['tl_page']['fields']['language']['default'] = $objPage->rootLanguage;
		}
	}

}//end class
?>
