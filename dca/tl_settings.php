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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .=
  ';{page_i18nl10n:hide},i18nl10n_languages,i18nl10n_default_language,'
  .'i18nl10n_alias_suffix,i18nl10n_addLanguageToUrl,i18nl10n_default_page';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_languages'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'],
    'exclude'   => true,
    'default'   => array('en','de','bg'),
    'inputType' => 'listWizard',
    'eval' => array('mandatory'=>true,
                    'style'=>'width:2em;','tl_class'=>'w50',
                    'nospace' => true
                    ),
    'save_callback' => array(
                             array('tl_settings_l10ns','ensureUnique'),
                             array('tl_settings_l10ns','ensureExists')
                           ) 
);

$i18nl10n_default_language = $this->Database
      ->prepare(
                "SELECT language FROM tl_page WHERE
                type='root'
                ORDER BY sorting"
      )->limit(1)->execute()->language;
$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_default_language'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'],
    'exclude'   => true,
    'inputType' => 'text',
    'input_field_callback'    => array('tl_settings_l10ns','showReadOnlyField'),
    'options' => array(
        $i18nl10n_default_language =>
        $GLOBALS['TL_LANG']['LNG'][$i18nl10n_default_language]
                       ), 
    'default'   =>  $i18nl10n_default_language,
    'eval' => array('mandatory'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_alias_suffix'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval' => array('tl_class'=>'w50'),
    'save_callback' => array(
         array('tl_settings_l10ns','ensureOthersUnchecked'),
       )
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_addLanguageToUrl'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval' => array(),
    'save_callback' => array(
         array('tl_settings_l10ns','ensureOthersUnchecked'),
       )
);

/*
$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_default_page'] =
  array(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_page'],
    'exclude'   => true,
    'inputType' => 'pageTree',
    'eval' => array('mandatory'=>true, 'unique'=>true,fieldType=>'radio')
);
*/









class tl_settings_l10ns extends Backend 
{
    function ensureUnique($languages, DataContainer $dc) {
        return serialize( array_unique( deserialize( $languages ) ) );
    }

    function ensureExists($languages, DataContainer $dc) {
      
        $array_language_exists = array();
        $array_languages = deserialize( $languages );
        $default_language_present = false;
        $i18nl10n_default_language = $this->Input->post('i18nl10n_default_language');
        if(!empty($array_languages))
        foreach($array_languages as $k) {
            if( array_key_exists($k, $GLOBALS['TL_LANG']['LNG']) ){
                array_push($array_language_exists,$k);
            }
            if($k == $i18nl10n_default_language){
              $default_language_present = true;
            }
        }
        //make sure default language is present
        if(!$default_language_present){
          $dc->addErrorMessage(
            $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingError']
          );
          return false;
        }
        return serialize( $array_language_exists );
    }                                                    
    
    function ensureOthersUnchecked($value, DataContainer $dc){
        //error_log($dc->field .':'.$value);
      if($dc->field == 'i18nl10n_alias_suffix' && $value)
        if($GLOBALS['TL_CONFIG']['addLanguageToUrl']
           ||$GLOBALS['TL_CONFIG']['i18nl10n_addLanguageToUrl']){
          $dc->addErrorMessage($GLOBALS['TL_LANG']['tl_settings']
                               ['i18nl10n_alias_suffixError']);
          $dc->log($GLOBALS['TL_LANG']['tl_settings']
                   ['i18nl10n_alias_suffixlError'],
                   __METHOD__, TL_CONFIGURATION);
        return false;
      }
        
      elseif($dc->field == 'i18nl10n_addLanguageToUrl' && $value)
        if($GLOBALS['TL_CONFIG']['addLanguageToUrl']
           ||$GLOBALS['TL_CONFIG']['i18nl10n_alias_suffix']){
          $dc->addErrorMessage($GLOBALS['TL_LANG']['tl_settings']
                               ['i18nl10n_addLanguageToUrlError']);
          $dc->log($GLOBALS['TL_LANG']['tl_settings']
                   ['i18nl10n_addLanguageToUrlError'],
                   __METHOD__, TL_CONFIGURATION);
        return false;
      }
    
    return $value;
    }
    
  public function showReadOnlyField(DataContainer $dc) {
    //log_message('$dc:'."$dc->id $dc->field $dc->value $dc->table");
    if($dc->field == 'i18nl10n_default_language'){
        return
         '<h3><label for="ctrl_'.$dc->field.'">'
        .$GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['label'][0]
        .'</label></h3>'
        .'<input type="hidden" readonly="readonly" name="'.
        $dc->field.'" value="'.$dc->value.'" />'
        .$GLOBALS['TL_LANG']['LNG'][$dc->value]
        ." ($dc->value)"
        .'<p class="tl_help tl_tip">'
        .$GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['label'][1]
        .'</p>';
    }

  }
}
