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


namespace Verstaerker\I18nl10n;

/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */

/**
 * Extend header includes
 */
if (TL_MODE == 'BE')
{
    /**
     * CSS files
     */
    $cssStyle = 'system/modules/i18nl10n/assets/css/style.css';

    if (is_array($GLOBALS['TL_CSS']))
    {
        $GLOBALS['TL_CSS'][] = $cssStyle;
    }
    else
    {
        $GLOBALS['TL_CSS'] = array($cssStyle);
    }
}


/**
 * Append module to sidebar
 */
$GLOBALS['BE_MOD']['design']['i18nl10n'] = array(
    'tables' => array('tl_page_i18nl10n'),
    'icon'   => 'system/modules/i18nl10n/assets/img/i18nl10n.png'
);


/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['FE_MOD']['navigationMenu']['i18nl10nnav'] = 'I18nL10nModuleLanguageNavigation';
$GLOBALS['FE_MOD']['navigationMenu']['breadcrumb']  = 'I18nL10nModuleBreadcrumb';


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */
$i18nl10nHooks = array
(
    'generateFrontendUrl' => array
    (
        array('\I18nl10n\Classes\I18nL10nHooks', 'generateFrontendUrl')
    ),
    'getPageIdFromUrl' => array
    (
        array('\I18nl10n\Classes\I18nL10nHooks', 'getPageIdFromUrl')
    ),
    'replaceInsertTags' => array
    (
        array('PageRegular', 'insertI18nL10nArticle')
    ),
    'getContentElement' => array
    (
        array('\I18nl10n\Classes\I18nL10nHooks', 'getContentElement')
    )
);

array_insert(
    $GLOBALS['TL_HOOKS'],
    count($GLOBALS['TL_HOOKS']),
    $i18nl10nHooks
);


/**
 * -------------------------------------------------------------------------
 * PAGE TYPES
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_PTY']['regular'] =  '\I18nl10n\Pages\I18nL10nPageRegular';

if(!$GLOBALS['TL_CONFIG']['i18nl10n_languages']){
    $GLOBALS['TL_CONFIG']['i18nl10n_languages'] = serialize(array('en'));
}