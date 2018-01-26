<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class CustomizeSearchHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Implementation of i18nl10n search logic.
 */
class CustomizeSearchHook
{
    /**
     * Add current language selector to search keywords
     *
     * Contao 3.3.5 +
     *
     * @param   array   $arrPages
     * @param   String  $strKeywords
     * @param   String  $strQueryType
     * @param   Boolean $blnFuzzy
     */
    public function customizeSearch($arrPages, &$strKeywords, $strQueryType, $blnFuzzy)
    {
        $strLanguage = $GLOBALS['TL_LANGUAGE'];
        $strKeywords .= " i18nl10n::$strLanguage";
    }
}
