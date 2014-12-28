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
$GLOBALS['TL_LANG']['tl_page']['l10n_published'] = array
(
    'Publish L10N',
    'Publish this translation.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_languages'] = array
(
    'Site languages',
    'Additional available languages/localizations for this page tree.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_language'] = 'Language';

/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page']['msg_no_languages']  =
    'No alternative languages have been defined yet. Please do so on the %s settings %s page.';
$GLOBALS['TL_LANG']['tl_page']['msg_multiple_root'] =
    'i18nl10n discovered multiple root pages in your page structure. Please be aware, that the module is not able to handle multiple page trees!';
$GLOBALS['TL_LANG']['tl_page']['msg_localize_all']  =
    'For all untranslated pages in <span style="white-space:nowrap">[%s]</span> I will create localizations. Are you sure you want to create the following localizations for all unlocalized pages?';