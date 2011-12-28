<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * PHP version 5
 * @copyright  Krasimir Berov 2010 
 * @author     Krasimir Berov 
 * @translated by nexflo
 * @package    MultiLanguagePage 
 * @license    LGPL3 
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['page_i18nl10n'] = 'Mehrsprachige Seiten';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'] = 
    array('Sprachen', 
          'Bitte fügen Sie
          <a target="_blank" 
          href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"><em> ISO 639-1</em></a>
          valide Sprach-Codes hinzu (z.B. <em>en</em> oder <em>de</em>).'
            .' Fügen Sie nur Ihre gewünschten Sprachen hinzu.');
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'] = 
    array('Standardsprache', 'Geben Sie die Standardsprache Ihrer Seite an. '
          .'Ein Content-Element in dieser Sprache sollte als Fallback immer vorhanden sein');
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'] =
    array (
        'Sprachkürzel als Suffix',
        'Die entsprechende Sprache wird in Echtzeit an den Alias angehängt. '
        . '(z.B. index - &gt; index.en, index.de usw.). '
        . 'The page language will be guessed from it too. '
        . 'Der eigentliche URL-Suffix wird zum Schluss angehängt (z.B. index.de.html). '
        . 'Beachten Sie! Dies ändert dynamisch erzeugte Links in Menüs, '
        .'aber nicht Ihre Seitenaliase. Sie können es jederzeit wieder ausschalten.'
    );

?>