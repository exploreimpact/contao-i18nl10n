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
 * @package     i18nl10n dca
 * @version     1.2.1
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

$this->loadLanguageFile('languages');

/**
 * i18nl10n settings palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace(
    ',addLanguageToUrl,',
    ',i18nl10n_urlParam,',
    $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']
);

/**
 * i18nl10n settings fields
 */
$i18nl10nSettings = array
(
    'i18nl10n_urlParam' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParam'],
        'exclude'   => true,
        'inputType' => 'radio',
        'default'   => 'parameter',
        'options'   => array('parameter', 'alias', 'url'),
        'reference' => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels'],
        'eval'      => array
        (
            'mandatory' => true,
            'tl_class' => 'w50 autoheight'
        ),
        'sql'       => "varchar(64) NOT NULL default ''"
    )
);

// insert i18nl10n fields
array_insert(
    $GLOBALS['TL_DCA']['tl_settings']['fields'],
    count($GLOBALS['TL_DCA']['tl_settings']['fields']),
    $i18nl10nSettings
);
