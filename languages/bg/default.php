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
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'] = 
    array('Език','Моля изберте един от наличните езици');
$GLOBALS['TL_LANG']['MSC']['editl10ns'] ='Редакция на преводите на страница %s';

$GLOBALS['TL_LANG']['MSC']['language'] = 'език';
//Allow unlocalized entries in tl_content so content elements can be shared among localized pages
$GLOBALS['TL_LANG']['LNG'][''] = 'Всеки';
