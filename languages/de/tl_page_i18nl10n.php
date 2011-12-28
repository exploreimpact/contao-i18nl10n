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
 * @translated by nexflo
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
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['menu_legend'] = 'Lokalisierte Felder für Menü und URL';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['meta_legend'] = 'Lokalisierte Meta-Felder für den &lt;head&gt;&lt;/head&gt; Bereich.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['time_legend'] = 'Lokalisierte Datum und Zeit Einstellungen';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['expert_legend'] = &$GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = &$GLOBALS['TL_LANG']['tl_page']['publish_legend'];

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['new']          = array('Neue L10N', 'Neue Lokalisierte Seite hinzufügen');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit']         = array('Bearbeiten', 'Lokalisierte Seite %s bearbeiten');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy']         = array('Duplizieren', 'Lokalisierte Seite %s duplizieren');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete']       = array('Löschen', 'Lokalisierte Seite %s löschen');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show']         = array('Anzeigen', 'Details der lokalisierten Seite %s anzeigen');
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all'] = array('L10N für Alle', 'Alle nicht lokalisierten Seiten mit vorhandenen Sprachen lokalisieren');

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all_q'] = 'Für alle nicht lokalisierten Seiten in <span style="white-space:nowrap">[%s]</span> werden lokalisierte Seiten in den vorhandenen Sprachen erstellt.<br />Sind Sie sicher, dass Sie für alle nicht lokalisierten Seiten, Lokalisierungen erstellen wollen?';

?>