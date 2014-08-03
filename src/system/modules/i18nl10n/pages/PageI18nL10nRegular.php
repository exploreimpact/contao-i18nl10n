<?php

/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * PHP version 5
 * @copyright   Verstärker, Patric Eberle 2014
 * @copyright   Krasimir Berov 2010-2013
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Krasimir Berov
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


namespace Verstaerker\I18nl10n\Pages;


/**
 * Class I18nPageRegular
 *
 * @copyright   Verstärker, Patric Eberle 2014
 * @copyright   Krasimir Berov 2010-2013
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Krasimir Berov
 * @package     i18nl10n
 */
class PageI18nl10nRegular extends \PageRegular
{
    /**
     * Override TL_PTY.regular
     *
     * @param $objPage
     * @param bool $blnCheckRequest
     */
    function generate($objPage, $blnCheckRequest = false)
    {
        self::fixupCurrentLanguage();

        if ($GLOBALS['TL_LANGUAGE'] == $GLOBALS['TL_CONFIG']['i18nl10n_default_language'])
        {
            if ($objPage->l10n_published == '')
            {
                header('HTTP/1.1 404 Not Found');
                $message = 'Page "'
                    . $objPage->alias
                    . '" is hidden for default language "'
                    . $objPage->language
                    . '". See "Publish settings/Hide default language" for Page ID '
                    . $objPage->id;
                $this->log($message, __METHOD__, TL_ERROR);
                die($message);
            }
            parent::generate($objPage);
        }

        //get language specific page properties
        $fields = 'title,language,pageTitle,description,cssClass,dateFormat,timeFormat,datimFormat,l10n_published,start,stop';

        $sql = "
            SELECT
              $fields
            FROM
              tl_page_i18nl10n
            WHERE
              pid = ?
              AND language = ?
        ";

        if(!BE_USER_LOGGED_IN)
        {
            $time = time();
            $sql .= "
                AND (start = '' OR start < $time)
                AND (stop = '' OR stop > $time)
                AND l10n_published = 1
            ";
        }

        $l10n = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($objPage->id, $GLOBALS['TL_LANGUAGE']);

        // if translated page, replace given fields in page object
        if ($l10n->numRows)
        {

            $objPage->defaultPageTitle = $objPage->pageTitle;
            $objPage->defaultTitle = $objPage->title;

            foreach (explode(',', $fields) as $field)
            {
                if ($l10n->$field)
                {
                    $objPage->$field = $l10n->$field;
                }
            }
        } // else at least replace language, to prevent language switch
        else
        {
            $objPage->language = $GLOBALS['TL_LANGUAGE'];
        }

        parent::generate($objPage);
    }


    /**
     * Fix up current language depending on momentary user preference.
     * Strangely $GLOBALS['TL_LANGUAGE'] is switched to the current user language if user is just
     * authentitcating and has the language property set.
     * See system/libraries/User.php:202
     * We override this behavior and let the user temporarily use the selected by him language.
     * One workaround would be to not let the members have a language property.
     * Then this method will not be needed any more.
     */
    private function fixupCurrentLanguage()
    {

        // TODO: Keep language if fallback

        // if language is added to url, get it from there
        if ($GLOBALS['TL_CONFIG']['i18nl10n_addLanguageToUrl'])
        {
            $this->import('Environment');
            $environment = $this->Environment;
             $basePath = preg_quote($GLOBALS['TL_CONFIG']['rewriteURL'] ? $GLOBALS['TL_CONFIG']['websitePath'] : $environment->scriptName);

             $regex = "@^($basePath/)?([A-z]{2}(?=/)){1}(/.*)@";

             // only set language if found in url
             if(preg_match($regex, $environment->requestUri))
            {
                 $_SESSION['TL_LANGUAGE'] = $GLOBALS['TL_LANGUAGE'] = preg_replace($regex, '$2', $environment->requestUri);
            }

             return;
            }

         $selectedLanguage = \Input::post('language');

         // allow GET request for language
         if(!$selectedLanguage){
            $selectedLanguage = \Input::get('language');
        }

        if ($selectedLanguage
            && in_array($selectedLanguage, deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages'])))
        {
            $_SESSION['TL_LANGUAGE'] = $GLOBALS['TL_LANGUAGE'] = $selectedLanguage;
        }
        elseif (isset($_SESSION['TL_LANGUAGE']))
        {
            $GLOBALS['TL_LANGUAGE'] = $_SESSION['TL_LANGUAGE'];
        }

    }


    /**
     * Generate content in the current language from articles
     * using insert tags.
     * A HOOK called in Controller::replaceInsertTags()!!
     * @param string $insert_tag The insert tag with the alias or id
     * @return string|boolean
     */
    public function insertI18nl10nArticle($insert_tag)
    {

        if (strpos($insert_tag, 'insert_i18nl10n_article') === false)
            return false;

        $tag = explode('::', $insert_tag);
        if (($strOutput = $this->getArticle($tag[1], false, true)) !== false) {
            return $this->replaceInsertTags(ltrim($strOutput));
        } else {
            return '<p class="error">'
            . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], $tag[1])
            . '</p>';
        }
    }
}