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
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

$GLOBALS['TL_DCA']['tl_news']['list']['operations']['i18nl10n'] = array
(
    'label'               => 'L10Ns',
    'href'                => 'do=i18nl10n',
    'button_callback'     => array('tl_page_l10ns', 'editl10ns')
);