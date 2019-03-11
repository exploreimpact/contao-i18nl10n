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
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Constants
 */
define('I18NL10N', '2.0.0');

/**
 * BACK END MODULES
 */
// Extend header includes
if (TL_MODE == 'BE') {
    // CSS files
    $GLOBALS['TL_CSS'][] = 'bundles/verstaerkeri18nl10n/css/style.css';

    // JS files
    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/verstaerkeri18nl10n/js/i18nl10n.js';
}

// Append be module to sidebar
array_insert(
    $GLOBALS['BE_MOD']['design'],
    array_search('page', array_keys($GLOBALS['BE_MOD']['design'])) + 1,
    array(
        'i18nl10n' => array(
            'tables' => array('tl_page_i18nl10n'),
            'icon'   => 'bundles/verstaerkeri18nl10n/img/i18nl10n.png'
        )
    )
);

/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['i18nl10n']['i18nl10nLanguageSelection'] = 'Verstaerker\I18nl10nBundle\Modules\ModuleI18nl10nLanguageSelection';


/**
 * HOOKS
 */
$GLOBALS['TL_HOOKS']['initializeSystem'][]    = array('Verstaerker\I18nl10nBundle\Hook\InitializeSystemHook', 'initializeSystem');
$GLOBALS['TL_HOOKS']['generateFrontendUrl'][] = array('Verstaerker\I18nl10nBundle\Hook\GenerateFrontendUrlHook', 'generateFrontendUrl');
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][]    = array('Verstaerker\I18nl10nBundle\Hook\GetPageIdFromUrlHook', 'getPageIdFromUrl');
$GLOBALS['TL_HOOKS']['generateBreadcrumb'][]  = array('Verstaerker\I18nl10nBundle\Hook\GenerateBreadcrumbHook', 'generateBreadcrumb');
$GLOBALS['TL_HOOKS']['executePostActions'][]  = array('Verstaerker\I18nl10nBundle\Hook\ExecutePostActionsHook', 'executePostActions');
$GLOBALS['TL_HOOKS']['isVisibleElement'][]    = array('Verstaerker\I18nl10nBundle\Hook\IsVisibleElementHook', 'isVisibleElement');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][]   = array('Verstaerker\I18nl10nBundle\Hook\ReplaceInsertTagsHook', 'replaceInsertTags');
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array('Verstaerker\I18nl10nBundle\Hook\LoadDataContainerHook', 'setLanguages');
$GLOBALS['TL_HOOKS']['getArticle'][]          = array('Verstaerker\I18nl10nBundle\Hook\GetArticleHook', 'checkIfEmpty');

// Append language selection for tl_content
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array('Verstaerker\I18nl10nBundle\Hook\LoadDataContainerHook', 'appendLanguageSelectCallback');

// Append button callback for tl_content to introduce permission
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array('Verstaerker\I18nl10nBundle\Hook\LoadDataContainerHook', 'appendButtonCallback');

// Append label callback for tl_article labels
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array('Verstaerker\I18nl10nBundle\Hook\LoadDataContainerHook', 'appendLabelCallback');

// Append child record callback for tl_content labels
$GLOBALS['TL_HOOKS']['loadDataContainer'][]   = array('Verstaerker\I18nl10nBundle\Hook\LoadDataContainerHook', 'appendChildRecordCallback');

// Search indexation
$GLOBALS['TL_HOOKS']['indexPage'][]           = array('Verstaerker\I18nl10nBundle\Hook\IndexPageHook', 'indexPage');
$GLOBALS['TL_HOOKS']['getSearchablePages'][]  = array('Verstaerker\I18nl10nBundle\Hook\GetSearchablePagesHook', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['customizeSearch'][]     = array('Verstaerker\I18nl10nBundle\Hook\CustomizeSearchHook', 'customizeSearch');


/**
 * PAGE TYPES
 */
$GLOBALS['TL_PTY']['regular'] = 'Verstaerker\I18nl10nBundle\Pages\PageI18nl10nRegular';


/**
 * Inherit language permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'i18nl10n_languages';


/**
 * Adding custom widgets
 */
$GLOBALS['BE_FFL']['i18nl10nMetaWizard'] = 'Verstaerker\I18nl10nBundle\Widgets\I18nl10nMetaWizard';
