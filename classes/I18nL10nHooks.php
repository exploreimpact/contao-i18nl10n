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

namespace Verstaerker\I18nl10n\Classes;


/**
 * Class I18nl10nHooks
 *
 * Provide Hooks to modify Contao
 * behaviour related to I18N and L10N.
 */
class I18nl10nHooks extends \System
{
    /**
     * Generates url for the site according to settings from the backend.
     *
     * Assumptions:
     * $GLOBALS['TL_CONFIG']['addLanguageToUrl'] == false;
     * $GLOBALS['TL_CONFIG']['useAutoItem'] == false;
     * TODO: create our own auto_item?
     *
     *
     */
    public function generateFrontendUrl($arrRow, $strParams, $strUrl)
    {
        if (!is_array($arrRow)) {
            throw new Exception('not an associative array.');
        }

        $language = (array_key_exists('robots', $arrRow) ? $GLOBALS['TL_LANGUAGE'] : $arrRow['language']);

        if (!$language) $language = $GLOBALS['TL_LANGUAGE'];

        $alias = $arrRow['alias'];

        // remove auto_item and language
        $strParams = preg_replace('@/auto_item|/language/[A-z]{2}@', '', $strParams);
        $strUrl = preg_replace('@/auto_item|/language/[A-z]{2}@', '', $strUrl);

        // get script name and prepare for regex
        $environment = $this->Environment->scriptName;
        if(strpos($environment, '/') == 0) {
            $environment = substr($environment, 1);
        }

        // if alias is disabled add language to get param end return
        if ($GLOBALS['TL_CONFIG']['disableAlias']) {

            $missingValueRegex = '@(.*\?[^&]*&)([^&]*)=(?=$|&)(&.*)?@';

            if ($GLOBALS['TL_CONFIG']['useAutoItem'] && preg_match($missingValueRegex, $strUrl) == 1) {
                $strUrl = preg_replace($missingValueRegex, '${1}auto_item=${2}${3}' , $strUrl);
            }

            return $strUrl . '&language=' . $language;
        }

        if ($GLOBALS['TL_CONFIG']['i18nl10n_alias_suffix'] && !$GLOBALS['TL_CONFIG']['disableAlias']) {
            $mystrUrl = $alias . $strParams . '.' . $language . $GLOBALS['TL_CONFIG']['urlSuffix'];

            // if rewrite is off, add environment
            if (!$GLOBALS['TL_CONFIG']['rewriteURL']) {
                $mystrUrl = $environment . '/' . $mystrUrl;
            }
        }
        elseif ($GLOBALS['TL_CONFIG']['i18nl10n_addLanguageToUrl']) {
            $mystrUrl = $language . '/' . $alias . $strParams . $GLOBALS['TL_CONFIG']['urlSuffix'];

            // if rewrite is off, add environment
            if(!$GLOBALS['TL_CONFIG']['rewriteURL']) {
                $mystrUrl = $environment . '/' . $mystrUrl;
            }

            // if alias is missing (f.ex. index.html), add it (exclude news!)
            // search for
            // www.domain.com/
            // www.domain.com/foo/
            if(!$GLOBALS['TL_CONFIG']['disableAlias'] && preg_match('@' . $arrRow['alias'] . '(?=\\' . $GLOBALS['TL_CONFIG']['urlSuffix'] . '|/)@', $mystrUrl) === false){
                $mystrUrl .= $alias . $GLOBALS['TL_CONFIG']['urlSuffix'];
            }

        }
        else {
            // if get variables
            if(strpos($strUrl, '?') !== false) {
                // if variable 'language' replace it
                if(strpos($strUrl, 'language=') !== false) {
                    $regex = "@language=[A-z]{2}@";
                    $mystrUrl = preg_replace(
                        $regex, 'language=' . $language, $strUrl
                    );
                } // if no variable 'language' add it
                else {
                    $mystrUrl = $strUrl . '&language=' . $language;
                }
            } // if no variables define variable 'language'
            else {
                $mystrUrl = $strUrl . '?language=' . $language;
            }
        }

        return $mystrUrl;
    }

    public function getPageIdFromUrl(Array $fragments)
    {
        global $TL_CONFIG;
        $this->import('Database');
        $fragments = array_map('urldecode', $fragments);
        $languages = deserialize($TL_CONFIG['i18nl10n_languages']);
        $language = $TL_CONFIG['i18nl10n_default_language'];

        // try to get language by i18nl10n URL
        if ($TL_CONFIG['i18nl10n_addLanguageToUrl']) {
            if (preg_match('@^([A-z]{2})$@', $fragments[0], $matches)) {
                $language = strtolower($matches[1]);
                array_push($fragments, 'language', $language);
            }
            $i = ($fragments[1] == 'auto_item' ? 2 : 1);
            $fragments[$i] = ($fragments[$i] ? $fragments[$i] : $TL_CONFIG['i18nl10n_default_page']);
            if (preg_match('@^([_\-\pL\pN\.]+)$@iu', $fragments[$i], $matches)) {
                $fragments[0] = $fragments[$i];
            }
            //TODO: solve "auto_item" issue
            $fragments = array_delete($fragments, $i);
        } // try to get language by suffix
        elseif ($TL_CONFIG['i18nl10n_alias_suffix'] && !$GLOBALS['TL_CONFIG']['disableAlias']) {
            $ok = preg_match('/^([_\-\pL\pN\.]+)\.([A-z]{2})$/u', $fragments[0], $matches);
            if ($ok) {
                $language = strtolower($matches[2]);
            }
            if ($ok && in_array($language, $languages)) {
                $fragments[0] = $matches[1];
                array_push($fragments, 'language', $language);
            }
        } // try to get language by query
        elseif ($this->Input->get('language')) {
            $language = $this->Input->get('language');
        }

        $time = time();
        $sql = "
        SELECT
            alias
        FROM
            tl_page
        WHERE
            (
                id=(SELECT pid FROM tl_page_i18nl10n WHERE id=? AND language=?)
                OR id=(SELECT pid FROM tl_page_i18nl10n WHERE alias=? AND language=?)
            )" . (!BE_USER_LOGGED_IN ? "
            AND (start='' OR start < $time)
            AND (stop='' OR stop > $time)
            AND published=1" : "");

        $objAlias = $this->Database->prepare($sql)
            ->execute(is_numeric($fragments[0] ? $fragments[0] : 0), $language, $fragments[0], $language);

        if ($objAlias->numRows)
        {
            $fragments[0] = $objAlias->alias;
        }

        return $fragments;
    }

    /**
     * Filter content elements by language
     *
     * @param ContentModel $objRow
     * @param String $strBuffer html string
     * @param Object $objElement content element object
     * @return string
     */
    public function getContentElement(\ContentModel $objRow, $strBuffer, $objElement) {
        $elemLanguage = $objRow->language;

        return ($elemLanguage == $GLOBALS['TL_LANGUAGE'] || $elemLanguage == '') ? $strBuffer : '';
    }


    /**
     *TODO if needed
     * function getRootPageFromUrl(){
     *   error_log( __METHOD__.':'.var_export($_GET,true) );
     *   return;
     * }
     */

}//end class
