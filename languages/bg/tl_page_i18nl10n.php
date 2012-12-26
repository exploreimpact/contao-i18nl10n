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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = array('', '');


/**
 * References
 */
//$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = 'tl_page meta reference text here';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['menu_legend'] = 'Локализирани полета, използвани в менюта и URL';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['meta_legend'] = 'Локализирани полета за мета-таговете в &lt;head&gt;&lt;/head&gt; секцията.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['time_legend'] = 'Локализирани формати за дата и време';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['expert_legend'] = &$GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = &$GLOBALS['TL_LANG']['tl_page']['publish_legend'];

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['new']    = array('Нова L10N', 
                                                      'добавя нова локализация на страница');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit']   = array('Редакция', 'Редакция на локализираните полета за страница %s');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy']   = array('Копиране', 'Копиране на локализираните полета');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'] = array('Изтриване', 'Изтриване на локализираните полета');
$GLOBALS['TL_LANG']['tl_page']['toggle'] = array('Публикуване/скриване на превод на страница', 'Публикуване/скриване на превод на страница с ID %s');


$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show']   = array('Виж', 'Локализирани полета');

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all']   = 
array('L10N за всички', 'Локализиране на всички непреведени страници на достъпните езици');

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all_q'] =
'За всички непреведени страници на <span style="white-space:nowrap">[%s]</span> ще се създе копие за всеки от наличните езици за сайта.<br />'
.'Сигурни ли сте, че искате да създадете локализации за всички нелокализирани страници?';

