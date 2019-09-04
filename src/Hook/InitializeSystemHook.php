<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Contao\Controller;
use Contao\System;
use Symfony\Component\HttpFoundation\RequestStack;
use Verstaerker\I18nl10nBundle\Classes\I18nl10n;
use Verstaerker\I18nl10nBundle\Exception\NoRootPageException;

/**
 * Class InitializeSystemHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Implementation of i18nl10n search logic.
 */
class InitializeSystemHook extends System
{
    /** @var  Request */
    protected $request;


    public function __construct()
    {
        parent::__construct();

        $this->import('request_stack', 'request_stack');
        $this->request = $this->request_stack->getCurrentRequest();
    }


    /**
     * @todo:   Refactor entirely as this approach does not work.
     *
     * @throws NoRootPageException
     */
    public function initializeSystem()
    {
        // Catch Facebook token fbclid and redirect without him (trigger 404 errors)...
        if (strpos(\Environment::get('request'), '?fbclid')) {
            \Controller::redirect(\strtok(\Environment::get('request'), '?'));
        }


        // Get locale information for system and user
        $arrLanguages = I18nl10n::getInstance()->getAvailableLanguages();
        $userLanguage = $this->request->getLocale();


        // Fail if no languages were configured
        if (\count($arrLanguages) === 0) {
            throw new NoRootPageException();
        }


        // Fallback to default language if language of request does not exist
        $languages = $arrLanguages[$_SERVER['HTTP_HOST']] ?: $arrLanguages['*'];
        if (!\in_array($userLanguage, $languages['languages'])) {
            $GLOBALS['TL_LANGUAGE'] = $languages['default'];
        }


        /*
        // If there is no request, add the browser language
        if ("" === \Environment::get('request')) {
            // check if the browser language is available
            $arrLanguages = I18nl10n::getInstance()->getAvailableLanguages();
            $userLanguage = \substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

            if (\count($arrLanguages) === 0) {
                throw new NoRootPageException();
            }

            $languages = $arrLanguages[$_SERVER['HTTP_HOST']] ?: $arrLanguages['*'];

            if (\in_array($userLanguage, $languages['languages'])) {
                $strRedirect = $userLanguage."/";
            } else {
                $strRedirect = $languages['default']."/";
            }

            // @todo:   Replace with other logic as this does not work as intended.
            //Controller::redirect($strRedirect);
        }
        */

        // If we are on the homepage, remove the urlSuffix
        $arrFragments = \explode("/", \Environment::get('request'));
        if ("" === $arrFragments[1] && "" != \Config::get('urlSuffix')) {
            \Config::set('tmpUrlSuffix', \Config::get('urlSuffix'));
            \Config::set('urlSuffix', "");
        }
    }
}
