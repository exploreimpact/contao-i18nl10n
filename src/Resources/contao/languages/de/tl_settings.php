<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParam'][0] = 'Die Sprache zur URL hinzufügen';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParam'][1] =
    'Definiert wie die Sprache in den URLs der Website verwendet wird.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['parameter'] = 'als Parameter (z.B. ?language=en)';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['alias'] = 'als Teil des Alias (z.B. home.en.html) [Funktioniert aktuell nicht]';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['url'] = 'als Teil der URL (z.B. mypage.com/en/index.html)';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_aliasSuffixError'] =
    'Es ist nicht möglich die Optionen <em>"%s"</em> und <em>"%s"</em> gleichzeitig zu aktivieren!';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_contaoAddLanguageToUrlError'] =
    'I18nl10n unterstützt die Contao Einstellung <em>"%s"</em> nicht. Bitte die Modul eigene, gleichnamige Option nutzen.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingInfo'] =
    'Die Standardsprache (Fallback) wurde nicht für die Seitensprachen definiert. Der fehlende Wert wurde automatisch hinzugefügt.';