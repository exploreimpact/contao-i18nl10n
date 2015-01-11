<?php
/**
 * i18nl10n Contao Module
 *
 * PHP version 5
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @version     1.2.0.rc
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
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_menuLegend']    = 'Lokalisierte Felder für Menüs und URLs';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_metaLegend']    = 'Lokalisierte Meta-Informationen';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_timeLegend']    = 'Lokalisierte Datums- und Zeiteinstellungen';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_expertLegend']  = &$GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = &$GLOBALS['TL_LANG']['tl_page']['publish_legend'];

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

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['toggle'] = array
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
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_root'] =
    'Es wurden noch keine Root-Seiten erstellt. Bitte hole dies über "%s" nach.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'] =
    'Es wurden noch keine alternativen Sprachen festgelegt. Bitte hole dies noch im jeweiligen Startpunkt unter "%s" nach.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_some_languages'] =
    'Einige Root-Seiten besitzen keine alternativen Sprachen.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'] =
    'Ich werde für jede fehlenden Übersetzung eine lokalisierte Seite gemäss der angezeigten Liste anlegen. Möchtest du wirklich fortfahren?';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['no_languages'] = 'Keine Sprachen';