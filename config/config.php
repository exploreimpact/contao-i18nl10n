<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */


/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 *
 * Back end modules are stored in a global array called "BE_MOD". Each module 
 * has certain properties like an icon, an optional callback function and one 
 * or more tables. Each module belongs to a particular group.
 * 
 *   $GLOBALS['BE_MOD'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'module_1' => array
 *           (
 *               'tables'       => array('table_1', 'table_2'),
 *               'key'          => array('Class', 'method'),
 *               'callback'     => 'ClassName',
 *               'icon'         => 'path/to/icon.gif',
 *               'stylesheet'   => 'path/to/stylesheet.css',
 *               'javascript'   => 'path/to/javascript.js'               
 *           )
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing modules array.
 */
if (TL_MODE == 'BE')
{
	/**
	 * CSS files
	 */

	if (is_array($GLOBALS['TL_CSS']))
	{
		$GLOBALS['TL_CSS'][] = 'system/modules/i18nl10n/html/style.css';
	}
	else
	{
		$GLOBALS['TL_CSS'] = array('system/modules/i18nl10n/html/style.css');
	}

	/**
	 * JavaScript files
	 */
	if (is_array($GLOBALS['TL_JAVASCRIPT']))
	{
		$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/i18nl10n/html/l10n.js';
	}
	else
	{
		$GLOBALS['TL_JAVASCRIPT'] = array('system/modules/i18nl10n/html/l10n.js');
	}
}

$GLOBALS['BE_MOD']['design']['i18nl10n'] = array(
    'tables' => array('tl_page_i18nl10n'),
    'icon'   => 'system/modules/i18nl10n/html/icon.png'
);
/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 *
 * List all fontend modules and their class names.
 * 
 *   $GLOBALS['FE_MOD'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'module_1' => 'Contentlass',
 *           'module_2' => 'Contentlass'
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing CTE array.
 */
$GLOBALS['FE_MOD']['navigationMenu']['i18nl10nnav'] = 'I18nL10nModuleLanguageNavigation';
$GLOBALS['FE_MOD']['navigationMenu']['breadcrumb']  = 'I18nL10nModuleBreadcrumb';
/**
 * -------------------------------------------------------------------------
 * CONTENT ELEMENTS
 * -------------------------------------------------------------------------
 *
 * List all content elements and their class names.
 * 
 *   $GLOBALS['TL_CTE'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'cte_1' => 'Contentlass',
 *           'cte_2' => 'Contentlass'
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing CTE array.
 */
 

/**
 * -------------------------------------------------------------------------
 * BACK END FORM FIELDS
 * -------------------------------------------------------------------------
 *
 * List all back end form fields and their class names.
 * 
 *   $GLOBALS['BE_FFL'] = array
 *   (
 *       'input'  => 'Class',
 *       'select' => 'Class'
 *   );
 * 
 * Use function array_insert() to modify an existing FFL array.
 */


/**
 * -------------------------------------------------------------------------
 * FRONT END FORM FIELDS
 * -------------------------------------------------------------------------
 *
 * List all form fields and their class names.
 * 
 *   $GLOBALS['TL_FFL'] = array
 *   (
 *       'input'  => Class,
 *       'select' => Class
 *   );
 * 
 * Use function array_insert() to modify an existing FFL array.
 */


/**
 * -------------------------------------------------------------------------
 * CACHE TABLES
 * -------------------------------------------------------------------------
 *
 * These tables are used to cache data and can be truncated using back end 
 * module "clear cache".
 * 
 *   $GLOBALS['TL_CACHE'] = array
 *   (
 *       'table_1',
 *       'table_2'
 *   );
 * 
 * Use function array_insert() to modify an existing cache array.
 */


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 *
 * Hooking allows you to register one or more callback functions that are 
 * called on a particular event in a specific order. Thus, third party 
 * extensions can add functionality to the core system without having to
 * modify the source code.
 * 
 *   $GLOBALS['TL_HOOKS'] = array
 *   (
 *       'hook_1' => array
 *       (
 *           array('Class', 'Method'),
 *           array('Class', 'Method')
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing hooks array.
 */
 
/** Potential candidates for implementation **
//$GLOBALS['TL_HOOKS']['getContentElement']
 */


$GLOBALS['TL_HOOKS']['generateFrontendUrl'][] =
	array('I18nL10nHooks', 'generateFrontendUrl');

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] =
	array('I18nL10nPageRegular', 'insertI18nL10nArticle');

$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] =
	array('I18nL10nHooks', 'getPageIdFromUrl');
// $GLOBALS['TL_HOOKS']['getRootPageFromUrl'][] =
//	array('I18nL10nHooks', 'getRootPageFromUrl');


/**
 * -------------------------------------------------------------------------
 * PAGE TYPES
 * -------------------------------------------------------------------------
 *
 * Page types and their corresponding front end controller class.
 * 
 *   $GLOBALS['TL_PTY'] = array
 *   (
 *       'type_1' => 'PageType1',
 *       'type_2' => 'PageType2'
 *   );                                      
 * 
 * Use function array_insert() to modify an existing page types array.
 */
 $GLOBALS['TL_PTY']['regular'] =  'I18nL10nPageRegular';
 
 if(!$GLOBALS['TL_CONFIG']['i18nl10n_languages']){ 
     $GLOBALS['TL_CONFIG']['i18nl10n_languages'] = serialize(array('en'));
 }


?>
