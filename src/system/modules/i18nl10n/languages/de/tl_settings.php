<?php
/**
 * i18nl10n Contao Module
 *
 * PHP version 5
 *
 * @copyright   &copy; 2015 Verstärker, Patric Eberle 2014
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['module_i18nl10n'] = 'Mehrsprachige Inhalte (i18nl10n)';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'] = array
(
    'Seitensprachen',
    'Definiere mit <a target="_blank" href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"><em>validen ISO 639-1</em></a> Sprach-Codes (z.B. <em>en</em> oder <em>de</em>) die verfügbaren Sprachen der Website. Die Standardsprache <strong>muss</strong> ebenfalls definiert sein!'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'] = array
(
    'Standardsprache (Fallback)',
    'Das ist die Fallbacksprache deiner Root-Seite und des i18nl10n Moduls.'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'] = array
(
    'Alias mit Sprachkürzel erweitern',
    'Die aktuell gewählte Sprache wird für die URL-Ausgabe dem Alias hinzugefügt (z.B. home &gt; home.en). Soll ein Suffix angezeigt werden, wird es nach dem Sprachkürzel angefügt (z.B. home.en.html). Hinweis: Diese Option wirkt sich nur auf die Ausgabe, nicht aber auf das Seitenalias selber aus!'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'] = array
(
    'Die Sprache zur URL hinzufügen',
    'Fügt den Sprachkürzel als ersten parameter der URL hinzu (z.B. <em>http://www.meine-website.com/en/</em>) und ist die Modulalternative zur gleichnamigen Contao Einstellung. Hinweis!: Bei der Verwendung dieser Option <em>muss</em> neben der gleichnamigen Contao Einstellung auch <em>"%s"</em> deaktiviert werden!'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffixError'] =
    'Es ist nicht möglich die Optionen <em>"%s"</em> und <em>"%s"</em> gleichzeitig zu aktivieren!';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_contaoAddLanguageToUrlError'] =
    'I18nl10n unterstützt die Contao Einstellung <em>"%s"</em> nicht. Bitte die Modul eigene, gleichnamige Option nutzen.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingInfo'] =
    'Die Standardsprache (Fallback) wurde nicht für die Seitensprachen definiert. Der fehlende Wert wurde automatisch hinzugefügt.';