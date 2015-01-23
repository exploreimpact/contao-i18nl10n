<?php
/**
 * i18nl10n Contao Module
 *
 * PHP version 5
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @version     1.2.1
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['module_i18nl10n'] = 'i18nl10n settings';

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_published'] = array
(
    'Publish L10N',
    'Publish this translation.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_localizations'] = array
(
    'Site localizations',
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
$GLOBALS['TL_LANG']['tl_page']['msg_missing_dns']  =
    'When using more than one root page with i18nl10n, every root page needs a unique domain name! One or more roots are missing this value!';
$GLOBALS['TL_LANG']['tl_page']['msg_duplicated_dns']  =
    'Some root pages us the same domain value. When using the i18nl10n module only one root page is allowed for a domain.';