<?php
/**
 * i18nl10n Contao Module
 *
 * PHP version 5
 *
 * @copyright   2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
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
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_menuLegend']    = 'Localized fields for menus and URL';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_metaLegend']    = 'Localized meta information';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_timeLegend']    = 'Localized date and time settings';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_expertLegend']  = &$GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = &$GLOBALS['TL_LANG']['tl_page']['publish_legend'];

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

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['toggle'] = array
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
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_root'] =
    'No root pages defined yet. Please do so on "%s"';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'] =
    'No alternative languages for i18nl10n have been defined yet. Please do so with the root pages on "%s".';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_some_languages'] =
    'Some root pages have no language alternatives.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'] =
    'I will create localizations for all missing translations based on the following list. Are you sure you want to continue?';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['no_languages'] = 'No languages';