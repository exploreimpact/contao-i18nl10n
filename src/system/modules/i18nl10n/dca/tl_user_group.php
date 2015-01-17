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
 * @version     1.2.0.rc
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

use Verstaerker\I18nl10n\Classes\I18nl10n;

$this->loadLanguageFile('languages');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = preg_replace(
    '@;{pagemounts_legend}@',
    ';{i18nl10n_legend},i18nl10n_languages;{pagemounts_legend}',
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
    'options_callback' => array('tl_user_group_i18nl10n', 'getAvailableLanguages'),
    'reference'        => &$GLOBALS['TL_LANG']['LNG'],
    'eval'             => array(
        'multiple' => true
    ),
    'sql'              => "blob NULL"
);


class tl_user_group_i18nl10n extends tl_user_group
{
    /**
     * Get available languages for user rights
     *
     * @return array
     */
    public function getAvailableLanguages() {
        return $this->mapLanguages(I18nl10n::getAllLanguages());
    }

    /**
     * Create domain related language array
     *
     * @param $arrLanguages
     *
     * @return array
     */
    private function mapLanguages($arrLanguages) {
        $arrMappedLanguages = array();

        // Loop Domains
        foreach ($arrLanguages as $domain => $config) {

            $arrLanguages = array(
                $config['rootId'] . '::*' => ''
            );

            // Loop languages
            foreach ($config['languages'] as $language) {
                // Create unique key by combining root id and language
                $strKey = $config['rootId'] . '::' . $language;

                // Add rootId to make unique
                $arrLanguages[$strKey] = $language;
            }

            $arrMappedLanguages[$domain] = $arrLanguages;
        }

        return $arrMappedLanguages;
    }
}