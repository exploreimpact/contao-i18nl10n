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
 * @copyright   Krasimir Berov 2010-2013
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Krasimir Berov
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


$this->loadLanguageFile('languages');

/**
 * Extend palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{page_i18nl10n:hide},i18nl10n_languages,i18nl10n_default_language,i18nl10n_alwaysShowL10n,i18nl10n_alias_suffix,i18nl10n_addLanguageToUrl';


/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_languages'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'],
    'exclude'   => true,
    'default'   => array('en','de','bg'),
    'inputType' => 'listWizard',
    'eval' => array(
        'mandatory'=>true,
        'style'=>'width:2em;','tl_class'=>'w50',
        'nospace' => true
    ),
    'save_callback' => array(
        array('tl_settings_l10n','ensureUnique'),
        array('tl_settings_l10n','ensureExists')
    )
);


$sql = "
    SELECT
      language
    FROM
      tl_page
    WHERE
      type = 'root'
    ORDER BY
      sorting
";

$i18nl10n_default_language = \Database::getInstance()
    ->prepare($sql)
    ->limit(1)
    ->execute()
    ->language;

$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_default_language'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'],
    'exclude'   => true,
    'inputType' => 'select',
    'options' => array
    (
        $i18nl10n_default_language => $GLOBALS['TL_LANG']['LNG'][$i18nl10n_default_language]
    ),
    'default'   =>  $i18nl10n_default_language,
    'eval' => array
    (
        'includeBlankOption' => true,
        'mandatory' => true,
        'tl_class' => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_alias_suffix'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval' => array
    (
        'tl_class'=>'w50 clr'
    ),
    'save_callback' => array
    (
        array('tl_settings_l10n','ensureOthersUnchecked'),
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_addLanguageToUrl'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval' => array
    (
        'tl_class'=>'w50'
    ),
    'save_callback' => array
    (
        array('tl_settings_l10n','ensureOthersUnchecked'),
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['i18nl10n_alwaysShowL10n'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alwaysShowL10n'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval' => array
    (
        'tl_class' => 'w50 clr'
    ),
);


class tl_settings_l10n extends Backend
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
        {
            foreach($array_languages as $k) {
                if( array_key_exists($k, $GLOBALS['TL_LANG']['LNG']) )
                {
                    array_push($array_language_exists,$k);
                }

                if($k == $i18nl10n_default_language)
                {
                    $default_language_present = true;
                }
            }
        }

        //make sure default language is present
        if(!$default_language_present){
            // print error message
            $errorMessage = $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingError'];
            \Message::addError($errorMessage);

            return false;
        }
        return serialize( $array_language_exists );
    }

    function ensureOthersUnchecked($value, DataContainer $dc){
        if($dc->field == 'i18nl10n_alias_suffix' && $value) {
            if($GLOBALS['TL_CONFIG']['addLanguageToUrl']
                || $GLOBALS['TL_CONFIG']['i18nl10n_addLanguageToUrl'])
            {
                // show error and write to log
                $errorMessage = $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffixError'];
                \Message::addError($errorMessage);
                \System::log($GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffixlError'], __METHOD__, TL_CONFIGURATION);

                return false;
            }
            elseif($dc->field == 'i18nl10n_addLanguageToUrl' && $value)
            {
                if($GLOBALS['TL_CONFIG']['addLanguageToUrl']
                    || $GLOBALS['TL_CONFIG']['i18nl10n_alias_suffix'])
                {
                    $errorMessage = $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrlError'];
                    \Message::addError($errorMessage);
                    \System::log($GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrlError'], __METHOD__, TL_CONFIGURATION);
                    return false;
                }
            }
        }

        return $value;
    }

    public function showReadOnlyField(DataContainer $dc) {
        if($dc->field == 'i18nl10n_default_language'){
            $html = '<h3><label for="ctrl_'.$dc->field.'">'
                . $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['label'][0]
                . '</label></h3>'
                . '<input type="hidden" readonly="readonly" name="' . $dc->field . '" value="' . $dc->value . '" />'
                . $GLOBALS['TL_LANG']['LNG'][$dc->value] . ' ' . ($dc->value)
                . '<p class="tl_help tl_tip">'
                . $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['label'][1]
                . '</p>';

            return $html;
        }

    }
}