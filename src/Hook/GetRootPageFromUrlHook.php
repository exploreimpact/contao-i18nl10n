<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Verstaerker\I18nl10nBundle\Classes\I18nl10n;

/**
 * Class GetRootPageFromUrlHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Implementation of i18nl10n request logic.
 */
class GetRootPageFromUrlHook extends \Controller
{
    /**
     * Catch empty requests and redirect them
     */
    public function getRootPageFromUrl()
    {
        // If there is no request, force url to the current language
        if ("" === \Environment::get('request')) {
            $this->redirect($GLOBALS['TL_LANGUAGE']."/");
        }
    }
}
