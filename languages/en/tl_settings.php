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
    array('Site Languages', 
          'Please add 
          <a target="_blank" 
          href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"><em>valid ISO 639-1</em></a>
          language codes(e.g. <em>en</em> or <em>bg</em>).'
            .' Add only those which you want to support.');

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'] = 
    array('Default Language', 'Enter a default language for your pages. '
          .'You should allways have a content element in this language.');

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'] = 
    array (
        'Use language as alias suffix',
        'The corresponding language will be appended  on the fly to the current page alias '
        . '(e.g. home -&gt; home.en,home.de etc.). '
        . 'The page language will be guessed from it too. '
        . 'URL suffix will be appended after it (e.g. home.en.html). '
        . 'Note! This will change dynamically generated links in menus, '
        .'but not your page aliases. You can always switch it off.'
    );
