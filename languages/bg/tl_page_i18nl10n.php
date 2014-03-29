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
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
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

