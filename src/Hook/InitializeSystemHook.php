<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Contao\Controller;
use Verstaerker\I18nl10nBundle\Classes\I18nl10n;
use Verstaerker\I18nl10nBundle\Exception\NoRootPageException;

/**
 * Class InitializeSystemHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Implementation of i18nl10n search logic.
 */
class InitializeSystemHook
{
    /**
     * @todo:   Refactor entirely as this approach does not work.
     *
     * @throws NoRootPageException
     */
    public function initializeSystem()
    {
        // Catch Facebook token fbclid and redirect without him (trigger 404 errors)...
        if (strpos(\Environment::get('request'), '?fbclid')) {
            \Controller::redirect(strtok(\Environment::get('request'), '?'));
        }

        // If there is no request, add the browser language
        if ("" === \Environment::get('request')) {
            // check if the browser language is available
            $arrLanguages = I18nl10n::getInstance()->getAvailableLanguages();
            $userLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

            if (count($arrLanguages) === 0) {
                throw new NoRootPageException();
            }

            $languages = $arrLanguages[$_SERVER['HTTP_HOST']] ?: $arrLanguages['*'];

            if (in_array($userLanguage, $languages['languages'])) {
                $strRedirect = $userLanguage."/";
            } else {
                $strRedirect = $languages['default']."/";
            }

            // @todo:   Replace with other logic as this does not work as intendet.
            //Controller::redirect($strRedirect);
        }

        // If we are on the homepage, remove the urlSuffix
        $arrFragments = explode("/", \Environment::get('request'));
        if ("" === $arrFragments[1] && "" != \Config::get('urlSuffix')) {
            \Config::set('tmpUrlSuffix', \Config::get('urlSuffix'));
            \Config::set('urlSuffix', "");
        }
    }
}
