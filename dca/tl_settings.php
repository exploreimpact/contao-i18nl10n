<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Krasimir Berov 2010 
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage 
 * @license    LGPL3 
 * @filesource
 */
 
$this->loadLanguageFile('languages');

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{page_i18nl10n:hide},i18nl10n_languages,i18nl10n_default_language';

/**
 * Add fields
 */
$GLOBALS['page_i18nl10n']['languages'] = $this->getLanguages();
$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_languages'] = array(
	'label'		=>	&$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'],
	'exclude'	=>	true,
	'default'   => array('bg','en','de'),
	'inputType' => 'listWizard',
    'eval' => array('mandatory'=>true,
                    'style'=>'width:2em;','tl_class'=>'w50'
                    ),
    'save_callback' => array(
                             array('tl_settings_l10ns','ensureUnique'),
                             array('tl_settings_l10ns','ensureExists')
                           ) 
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_default_language'] = array(
	'label'		=>	&$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'],
	'exclude'	=>	true,
	'inputType' => 'select',
	'default'   => 'en',
    'options'   => $GLOBALS['page_i18nl10n']['languages'],
    'eval' => array('mandatory'=>true,
                    'style'=>'width:15em;','tl_class'=>'w50','unique'=>true
                    )
);


class tl_settings_l10ns extends Backend 
{
    function ensureUnique($languages, DataContainer $dc) {
        return serialize( array_unique( deserialize( $languages ) ) );
    }
    function ensureExists($languages, DataContainer $dc) {
        $array_language_exists = array();
        $array_languages = deserialize( $languages );
        
        foreach( $array_languages as $i => $k ) {
            if( array_key_exists($k, $GLOBALS['TL_LANG']['LNG']) ){
                array_push($array_language_exists,$k);
            }
        }
        return serialize( $array_language_exists );
    }
}






?>
