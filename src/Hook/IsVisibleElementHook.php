<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class IsVisibleElementHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Determine when to show content elements in relation to the current page language.
 */
class IsVisibleElementHook
{
    /**
     * Only make elements visible, that belong to this or all languages
     *
     * @param $objElement
     * @param $blnIsVisible
     *
     * @return mixed
     */
    public function isVisibleElement($objElement, $blnIsVisible)
    {
        global $objPage;

        // Show all contents in Backend
        if (TL_MODE == 'BE') {
            return true;
        }

        if ($blnIsVisible && $objElement->language) {
            // Check if given language is valid or fallback should be used
            $strLanguage = $objPage->useFallbackLanguage
                ? $objPage->rootLanguage
                : $GLOBALS['TL_LANGUAGE'];

            $blnIsVisible = $objElement->language === $strLanguage;
        }

        return $blnIsVisible;
    }
}
