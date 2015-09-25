<?php
/**
 * i18nl10n Contao Module
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['module_i18nl10n'] = 'i18nl10n-Einstellungen';

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_published'] = array
(
    'L10N veröffentlichen',
    'Diese Übersetzung veröffentlichen.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_localizations'] = array
(
    'Seiten Lokalisierungen',
    'Alternative Sprachen/Lokalisierungen, die für diesen Seitenast zur Verfügung stehen.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_language'] = 'Sprache';

/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page']['msg_no_languages']  =
    'Es wurden noch keine alternativen Sprachen festgelegt. Bitte hole dies noch in den %s Einstellungen %s nach.';
$GLOBALS['TL_LANG']['tl_page']['msg_multiple_root'] =
    'i18nl10n hat in deiner Seitenstruktur mehr als eine Root-Seite gefunden. Bitte beachte, dass das Modul nicht für die Verwendung mehrerer Root-Seiten ausgelegt ist!';
$GLOBALS['TL_LANG']['tl_page']['msg_missing_dns']  =
    'Bei der Verwendung von mehr als einer Root-Seite zusammen mit dem i18nl10n Modul muss für jede Root-Seite ein eindeutiger Domainname vergeben werden! Einer oder mehreren Seiten fehlt dieser Wert!';
$GLOBALS['TL_LANG']['tl_page']['msg_duplicated_dns']  =
    'Einige Root-Seiten besitzen den gleichen DNS-Wert. Bei der Verwendung des Modules i18nl10n ist pro Domain nur eine Root-Seite erlaubt.';