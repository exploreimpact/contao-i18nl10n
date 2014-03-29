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
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['i18nl10n'] = array('Multilanguage Pages', 
     'Create a one-pagetree multi-language site by 
      adding multiple languages to pages 
     and localizing content elements.');

/**
 * Front end modules
 */
//$GLOBALS['TL_LANG']['FMD'][''] = array('', '');
$GLOBALS['TL_LANG']['FMD']['i18nl10nnav']     = array('Languages menu', 'Generates flat menu to navigate between page and content languages.');

