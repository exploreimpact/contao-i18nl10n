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


// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['i18nl10nnav'] = '{title_legend},name,headline,type;'
.'{template_legend:hide},navigationTpl;{protected_legend:hide},protected;'
.'{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['fields']['navigationTpl']['default']['nav_18nl10n_language_menu']
?>
