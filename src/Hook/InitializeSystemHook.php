<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Contao\Controller;
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
        // If there is no request, add the browser language
        if ("" === \Environment::get('request')) {
            // check if the browser language is available
            $arrLanguages = I18nl10n::getInstance()->getAvailableLanguages();
            $userLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

            $languages = $arrLanguages[$_SERVER['HTTP_HOST']] ?: $arrLanguages['*'];

            if (in_array($userLanguage, $languages['languages'])) {
                $strRedirect = $userLanguage."/";
            } else {
                $strRedirect = $languages['default']."/";
            }

            Controller::redirect($strRedirect);
        }

        // If we are on the homepage, remove the urlSuffix
        $arrFragments = explode("/", \Environment::get('request'));
        if ("" === $arrFragments[1] && "" != \Config::get('urlSuffix')) {
            \Config::set('tmpUrlSuffix', \Config::get('urlSuffix'));
            \Config::set('urlSuffix', "");
        }
    }
}
