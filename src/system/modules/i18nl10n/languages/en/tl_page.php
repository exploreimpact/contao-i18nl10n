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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['l10n_published'] = array
(
    'Publish L10N',
    'Publish this translation.'
);


/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page']['msg_no_languages'] =
    'No alternative languages have been defined yet. Please do so on the '
    . '%s settings %s page.';

$GLOBALS['TL_LANG']['tl_page']['msg_localize_all'] =
    'For all untranslated pages in <span style="white-space:nowrap">[%s]</span> '
    . 'I will create localizations. Are you sure you want to create the following localizations for all unlocalized pages?';