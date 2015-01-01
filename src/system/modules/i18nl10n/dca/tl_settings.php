<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

$this->loadLanguageFile('languages');

/**
 * Update label
 */
$i18nl10n_addLanguageToUrlLabel = &$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'];
$i18nl10n_addLanguageToUrlLabel[1] = sprintf($GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'][1], $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'][0]);

/**
 * i18nl10n settings palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{module_i18nl10n:hide},i18nl10n_languages,i18nl10n_default_language,i18nl10n_alias_suffix,i18nl10n_addLanguageToUrl';

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
        'options'   => array('parameter', 'alias', 'path'),
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['i18nl10n_langStyleLabels'],
        'eval'      => array
        (
            'tl_class' => 'w50 autoheight'
        ),
        'sql'       => "varchar(64) NOT NULL default ''"
    )
);

// inset i18nl10n fields
array_insert(
    $GLOBALS['TL_DCA']['tl_settings']['fields'],
    count($GLOBALS['TL_DCA']['tl_settings']['fields']),
    $i18nl10nSettings
);


/**
 * Class tl_settings_l10n
 *
 * @copyright   2015 Verstärker, Patric Eberle
 * @copyright   Krasimir Berov 2010-2011
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Krasimir Berov <http://i-can.eu>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */
class tl_settings_l10n extends Backend
{

    /**
     * Ensure Contao add language to url is disabled
     *
     * @param $value
     * @param DataContainer $dc
     * @return bool
     */
    function ensureOthersUnchecked($value, DataContainer $dc)
    {

        if ($value
            && ($dc->field == 'i18nl10n_alias_suffix'
                && \Config::get('i18nl10n_addLanguageToUrl') == 1
                || $dc->field == 'i18nl10n_addLanguageToUrl'
                && \Config::get('i18nl10n_alias_suffix') == 1)
        )
        {

            // show error and write to log
            $errorMessage = & $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_aliasSuffixError'];

            $errorMessage = sprintf(
                $errorMessage,
                $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'][0],
                $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'][0]
            );

            \Message::addError($errorMessage);
            \System::log($errorMessage, __METHOD__, TL_CONFIGURATION);

            return false;
        }
        elseif (\Config::get('addLanguageToUrl'))
        {

            $errorMessage = & $GLOBALS['TL_LANG']['tl_settings']['i18nl10n_contaoAddLanguageToUrlError'];
            $errorMessage = sprintf(
                $errorMessage,
                $GLOBALS['TL_LANG']['tl_settings']['addLanguageToUrl'][0]
            );
            \Message::addError($errorMessage);
            \System::log($errorMessage, __METHOD__, TL_CONFIGURATION);
            return false;
        }


        return $value;
    }

}