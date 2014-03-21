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
 
//TODO make those includes more OO and use $this->loadLanguageFile()
//include_once(TL_ROOT.'/system/modules/backend/languages/en/tl_page.php');


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = array('', '');


/**
 * References
 */
//$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = 'tl_page meta reference text here';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['menu_legend'] = 'Localized fields for menus and URL';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['meta_legend'] = 'Localized meta-fields for the &lt;head&gt;&lt;/head&gt; section.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['time_legend'] = 'Localized date and time settings';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['expert_legend'] = &$GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = &$GLOBALS['TL_LANG']['tl_page']['publish_legend'];

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['new']    = array('New L10N', 
                                                      'Add new localization for page');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit']   = array('Edit', 'Edit meta-fields for page %s');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy']   = array('Copy', 'Copy meta-fields');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'] = array('Delete', 'Delete meta-fields');
$GLOBALS['TL_LANG']['tl_page']['toggle']     = array('Publish/unpublish L10N', 'Publish/unpublish L10N ID %s');

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show']   = array('Show', 'meta-fields');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all']   = 
array('L10N for аll', 'Localize all untranslated pages to available languages');

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all_q'] =
'For all untranslated pages in <span style="white-space:nowrap">[%s]</span> will be created copies for each of the site languages.<br />'
.'Are you sure you want to create localizations for all unlocalized pages?';

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_add_language_to_url'] = 'i18nl10n is not compatible with the "Add language to URL" function of Contao. Please use the i18nl10n alternative from the settings page.';