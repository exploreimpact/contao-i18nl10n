<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
?>
