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
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_legend'] = 'L10N Settings';

$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = array
(
    '',
    ''
);


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['menu_legend'] = 'Localized fields for menus and URL';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['meta_legend'] = 'Localized meta information';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['time_legend'] = 'Localized date and time settings';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['expert_legend'] = & $GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = & $GLOBALS['TL_LANG']['tl_page']['publish_legend'];


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['new'] = array
(
    'New L10N',
    'Add new localization for page'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['define_language'] = array
(
    'Languages',
    'Define languages on settings page'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit'] = array
(
    'Edit',
    'Edit localization %s'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy'] = array
(
    'Copy',
    'Copy localization'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'] = array
(
    'Delete',
    'Delete localization'
);

$GLOBALS['TL_LANG']['tl_page']['toggle'] = array
(
    'Publish/unpublish L10N',
    'Publish/unpublish L10N ID %s'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show'] = array
(
    'Show',
    'meta-fields'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all'] = array
(
    'L10N for аll',
    'Localize all untranslated pages to available languages'
);


/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'] =
    'No alternative languages for i18nl10n have been defined yet. Please do so on the %s settings page %s.';

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'] =
    'For all untranslated pages in <span style="white-space:nowrap">[%s]</span> '
    . 'I will create localizations. Are you sure you want to create the following localizations for all unlocalized pages?';
