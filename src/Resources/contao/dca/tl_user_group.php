<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n dca
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

$this->loadLanguageFile('languages');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = preg_replace(
    '@;{pagemounts_legend@',
    ';{i18nl10n_legend},i18nl10n_languages;{pagemounts_legend',
    $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']
);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['i18nl10n_languages'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_user_group']['i18nl10n_languages'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => array('Verstaerker\I18nl10nBundle\Classes\I18nl10n', 'getLanguageOptionsForUserOrGroup'),
    'reference'        => &$GLOBALS['TL_LANG']['LNG'],
    'eval'             => array(
        'multiple' => true
    ),
    'sql'              => 'blob NULL'
);