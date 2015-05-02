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
 * @version     1.4.0
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * BACK END MODULES
 */
// Extend header includes
if (TL_MODE == 'BE') {
    // CSS files
    $GLOBALS['TL_CSS'][] = 'system/modules/i18nl10n/assets/css/style.css';

    // JS files
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/i18nl10n/assets/js/i18nl10n.js';
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
$strClassName = '\I18nl10n\Classes\I18nl10nHook';

$GLOBALS['TL_HOOKS']['generateFrontendUrl'][] = array($strClassName, 'generateFrontendUrl');
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][]    = array($strClassName, 'getPageIdFromUrl');
$GLOBALS['TL_HOOKS']['generateBreadcrumb'][]  = array($strClassName, 'generateBreadcrumb');
$GLOBALS['TL_HOOKS']['executePostActions'][]  = array($strClassName, 'executePostActions');
$GLOBALS['TL_HOOKS']['isVisibleElement'][]    = array($strClassName, 'isVisibleElement');
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array($strClassName, 'appendLanguageSelectCallback');
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array($strClassName, 'appendButtonCallback');
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array($strClassName, 'setIsotopeLanguages');
$GLOBALS['TL_HOOKS']['indexPage'][]           = array($strClassName, 'indexPage');
$GLOBALS['TL_HOOKS']['getSearchablePages'][]  = array($strClassName, 'getSearchablePages');
$GLOBALS['TL_HOOKS']['customizeSearch'][]     = array($strClassName, 'customizeSearch');

/**
 * PAGE TYPES
 */
$GLOBALS['TL_PTY']['regular'] = '\I18nl10n\Pages\PageI18nl10nRegular';

/**
 * Inherit language permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'i18nl10n_languages';

/**
 * Adding custom widgets
 */
$GLOBALS['BE_FFL']['i18nl10nMetaWizard'] = 'Verstaerker\i18nl10n\Widgets\I18nl10nMetaWizard';
