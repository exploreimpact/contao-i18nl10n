<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n pages
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace Verstaerker\I18nl10n\Pages;

use Verstaerker\I18nl10n\Classes\I18nl10n;


/**
 * Class I18nPageRegular
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 */
class PageI18nl10nRegular extends \PageRegular
{

    /**
     * Languages for current domain
     *
     * @var array
     */
    private $domainLanguages = null;

    /**
     * Construct object
     */
    function __construct() {
        // Get domain languages
        $this->domainLanguages = i18nl10n::getInstance()->getLanguagesByDomain();
    }

    /**
     * Generate FE page
     * Override TL_PTY.regular
     *
     * @param      $objPage
     * @param bool $blnCheckRequest
     */
    function generate($objPage, $blnCheckRequest = false)
    {
        self::fixupCurrentLanguage();

        // Check if default language
        if ($GLOBALS['TL_LANGUAGE'] === $this->domainLanguages['default']) {

            // if default language is not published, give error
            if (empty($objPage->i18nl10n_published)) {
                /** @var  \Contao\PageError404  $objError */
                $objError = new $GLOBALS['TL_PTY']['error_404']();
                $objError->generate($objPage->id);
            }

            self::addAlternativeLanguageLinks($objPage);

            parent::generate($objPage, $blnCheckRequest);
            return;
        }

        // Try to get translated page
        $objPage = I18nl10n::getInstance()->findPublishedL10nPage($objPage);

        // If neither fallback nor localization are published and null
        // was given, give error 404
        if (!$objPage) {
            /** @var  \Contao\PageError404  $objError */
            $objError = new $GLOBALS['TL_PTY']['error_404']();
            $objError->generate($objPage->id);
        }

        self::addAlternativeLanguageLinks($objPage);

        parent::generate($objPage, $blnCheckRequest);
    }

    /**
     * Fix up current language depending on momentary user preference.
     *
     * Strangely $GLOBALS['TL_LANGUAGE'] is switched to the current user language if user is just
     * authenticating and has the language property set.
     * See system/libraries/User.php:202
     * We override this behavior and let the user temporarily use the selected by him language.
     * One workaround would be to not let the members have a language property.
     * Then this method will not be needed any more.
     */
    private function fixupCurrentLanguage()
    {
        // Try to get language from post (committed by language select) or get
        $selectedLanguage = \Input::get('language');

        // If selected language is found already, use it
        if ($selectedLanguage) {
            $GLOBALS['TL_LANGUAGE'] = $_SESSION['TL_LANGUAGE'] = $selectedLanguage;
            return;
        }

        // if language is part of alias
        if (\Config::get('i18nl10n_urlParam') === 'alias') {
            $this->import('Environment');
            $requestUri  = $this->Environment->requestUri;
            $strUrlSuffix = preg_quote(\Config::get('urlSuffix'));

            $regex = "@.*?\.([a-z]{2})$strUrlSuffix@";

            // only set language if found in url
            if (preg_match($regex, $requestUri)) {
                $_SESSION['TL_LANGUAGE'] = $GLOBALS['TL_LANGUAGE'] = preg_replace($regex, '$1', $requestUri);
                return;
            }
        }

        // If everything failed yet use session language if part of domain languages, else use fallback
        if (in_array($_SESSION['TL_LANGUAGE'], (array) $this->domainLanguages['languages'])) {
            $GLOBALS['TL_LANGUAGE'] = $_SESSION['TL_LANGUAGE'];
        } else {
            $GLOBALS['TL_LANGUAGE'] = $_SESSION['TL_LANGUAGE'] = $this->domainLanguages['default']; // replace with fallback language of domain
        }
    }

    /**
     * Add alternative language links to page head
     *
     * @param   \PageModel  $objPage
     */
    private function addAlternativeLanguageLinks($objPage) {
        $arrPages = I18nl10n::getInstance()->getLanguageAlternativesByPage($objPage);
        $links = array();

        foreach($arrPages as $page) {
            $page['forceRowLanguage'] = true;
            $strUrl = \Controller::generateFrontendUrl($page);

            $links[] = "<link rel=\"alternate\" href=\"/{$strUrl}\" hreflang=\"{$page['language']}\" title=\"{$page['title']}\" />";
        }

        // Append alternative links to page header
        $GLOBALS['TL_HEAD'] = array_merge(
            (array) $GLOBALS['TL_HEAD'],
            $links
        );
    }
}
