<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class IndexPageHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Implementation of i18nl10n search indexing logic.
 */
class IndexPageHook
{
    /**
     * Add language selector to page indexing string
     *
     * @param $strContent
     * @param $arrData
     * @param $arrSet
     */
    public function indexPage(&$strContent, $arrData, $arrSet)
    {
        $strContent .= ' i18nl10n::' . $arrData['language'] . ' ';
    }
}
