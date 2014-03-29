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
$GLOBALS['TL_LANG']['tl_settings']['page_i18nl10n'] = 'Multilanguage Pages';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'] = 
    array('Езици за сайта', 
          'Моля, добавете
          <a target="_blank" 
          href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"><em>валидни ISO 639-1</em></a>
          езикови кодове(напр. <em>en</em> or <em>bg</em>).'
            .' Добавете само тези, които искате сайта да поддържа.');
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'] = 
    array('Език по подразбиране', 'Въвъедете език по подразбиране за страниците. '
          .'Трябва винаги да имате елемент със съдържание на този език.');
