<?php

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
 * Legends & Fields
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_legend'] = 'L10N Einstellungen';

$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = array
(
    '',
    ''
);


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['menu_legend'] = 'Lokalisierte Felder für Menüs und URLs';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['meta_legend'] = 'Lokalisierte Meta-Felder des &lt;head&gt;&lt;/head&gt; Bereichs.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['time_legend'] = 'Lokalisierte Datums- und Zeiteinstellungen';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['expert_legend'] = & $GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = & $GLOBALS['TL_LANG']['tl_page']['publish_legend'];


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['new'] = array
(
    'Neue L10N',
    'Eine neue Lokalisierung für eine Seite erstellen'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['define_language'] = array
(
    'Sprachen',
    'Verfügbare Sprachen in den Einstellungen festlegen'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit'] = array
(
    'Bearbeiten',
    'Lokalisierung mit der ID %s bearbeiten'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy'] = array
(
    'Kopieren',
    'Lokalisierung kopieren'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'] = array
(
    'Löschen',
    'Lokalisierung löschen'
);

$GLOBALS['TL_LANG']['tl_page']['toggle'] = array
(
    'L10N veröffentlichen/unveröffentlichen',
    'L10N mit der ID %s veröffentlichen/unveröffentlichen'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show'] = array
(
    'Show',
    'Meta-Informationen'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all'] = array
(
    'L10N für alle',
    'Alle Seiten in allen verfügbaren Sprachen lokalisierung.'
);


/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'] =
    'Es wurden noch keine alternativen Sprachen festgelegt. Bitte hole dies noch in den '
    . '<a href="%s">Einstellungen</a> nach.';

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'] =
    'Für alle Seiten in <span style="white-space:nowrap">[%s]</span> ohne Übersetzung '
    . 'werden Lokalisierungen angelegt. Bist du sicher, dass du für die folgenden Sprachen Seiten anlegen möchtest?';
