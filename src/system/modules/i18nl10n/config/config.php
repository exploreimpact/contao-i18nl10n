<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n config
 * @version     1.2.1
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * BACK END MODULES
 */

// Extend header includes
if (TL_MODE == 'BE')
{
    // CSS files
    $strCss = 'system/modules/i18nl10n/assets/css/style.css';

    if (is_array($GLOBALS['TL_CSS'])) {
        $GLOBALS['TL_CSS'][] = $strCss;
    } else {
        $GLOBALS['TL_CSS'] = array($strCss);
    }

    // JS files
    $strJs = 'system/modules/i18nl10n/assets/js/i18nl10n.js';

    if (is_array($GLOBALS['TL_JAVASCRIPT'])) {
        $GLOBALS['TL_JAVASCRIPT'][] = $strJs;
    } else {
        $GLOBALS['TL_JAVASCRIPT'] = array($strJs);
    }
}


// Append be module to sidebar
array_insert(
    $GLOBALS['BE_MOD']['design'],
    array_search('page', array_keys($GLOBALS['BE_MOD']['design'])) + 1,
    array
    (
        'i18nl10n' => array
        (
            'tables' => array('tl_page_i18nl10n'),
            'icon'   => 'system/modules/i18nl10n/assets/img/i18nl10n.png'
        )
    )
);


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['i18nl10n']['i18nl10nLanguageSelection'] = '\I18nl10n\Modules\ModuleI18nl10nLanguageSelection';


/**
 * HOOKS
 */
$GLOBALS['TL_HOOKS']['generateFrontendUrl'][] = array('\I18nl10n\Classes\I18nl10nHook', 'generateFrontendUrl');
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('\I18nl10n\Classes\I18nl10nHook', 'getPageIdFromUrl');
$GLOBALS['TL_HOOKS']['generateBreadcrumb'][] = array('\I18nl10n\Classes\I18nl10nHook', 'generateBreadcrumb');
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('\I18nl10n\Classes\I18nl10nHook', 'executePostActions');
$GLOBALS['TL_HOOKS']['isVisibleElement'][] = array('\I18nl10n\Classes\I18nl10nHook', 'isVisibleElement');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('\I18nl10n\Classes\I18nl10nHook', 'appendLanguageSelectCallback');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('\I18nl10n\Classes\I18nl10nHook', 'appendButtonCallback');
$GLOBALS['TL_HOOKS']['indexPage'][] = array('\I18nl10n\Classes\I18nl10nHook', 'indexPage');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('\I18nl10n\Classes\I18nl10nHook', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['customizeSearch'][] = array('\I18nl10n\Classes\I18nl10nHook', 'customizeSearch');


/**
 * PAGE TYPES
 */
$GLOBALS['TL_PTY']['regular'] = '\I18nl10n\Pages\PageI18nl10nRegular';

/**
 * Inherit language permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'i18nl10n_languages';
