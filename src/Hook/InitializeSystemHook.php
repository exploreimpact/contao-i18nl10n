<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Verstaerker\I18nl10nBundle\Classes\I18nl10n;

/**
 * Class InitializeSystemHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Implementation of i18nl10n search logic.
 */
class InitializeSystemHook
{
    public function initializeSystem()
    {
        // If there is no request, add the current language
        if ("" === \Environment::get('request')) {
            \Contao\Controller::redirect($GLOBALS['TL_LANGUAGE']."/");
        }

        // If we are on the homepage, remove the urlSuffix
        $arrFragments = explode("/", \Environment::get('request'));
        if ("" === $arrFragments[1] && "" != \Config::get('urlSuffix')) {
            \Config::set('tmpUrlSuffix', \Config::get('urlSuffix'));
            \Config::set('urlSuffix', "");
        }
    }
}
