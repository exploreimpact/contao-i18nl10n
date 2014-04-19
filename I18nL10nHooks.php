<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


/**
 * Class I18nL10nHooks
 *
 * Provide Hooks to modify Contao
 * behaviour related to I18N and L10N.
 */
class I18nL10nHooks extends System
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
    public function generateFrontendUrl($arrRow, $strParams='', $strUrl='')
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

            $missingValueRegex = '@(.*\?.*)(?<=&)(.*)=(?=$|&)(&.*)?@';

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


    /**
     * Correct page id based on language
     *
     * @param array $arrFragments
     * @return array
     */
    public function getPageIdFromUrl(Array $arrFragments)
    {
        global $TL_CONFIG;
        $this->import('Database');
        $arrFragments = array_map('urldecode', $arrFragments);
        $languages = deserialize($TL_CONFIG['i18nl10n_languages']);
        $language = $TL_CONFIG['i18nl10n_default_language'];

        // strip auto_item
        if ($GLOBALS['TL_CONFIG']['useAutoItem'] && $arrFragments[1] == 'auto_item') {
            $arrFragments = array_delete($arrFragments, 1);
        }

        // try to get language by i18nl10n URL
        if ($TL_CONFIG['i18nl10n_addLanguageToUrl']) {
                if (preg_match('@^([A-z]{2})$@', $arrFragments[0], $matches)) {
                $language = strtolower($matches[1]);

                // remove old language entry
                $arrFragments = array_delete($arrFragments, 0);

                // append new language entry
                array_push($arrFragments, 'language', $language);
            }
        } // try to get language by suffix
        elseif ($TL_CONFIG['i18nl10n_alias_suffix'] && !$GLOBALS['TL_CONFIG']['disableAlias']) {
            // last element should contain language info
            if(preg_match('@^([_\-\pL\pN\.]*(?=\.))?\.?([A-z]{2})$@u', $arrFragments[count($arrFragments) - 1], $matches)) {

                // define language and alias value
                $language = strtolower($matches[2]);
                $alias = $matches[1] != '' ? $matches[1] : $arrFragments[count($arrFragments) - 1];

                if(in_array($language, $languages)) {

                    // if only language was found, pop it from array
                    if($matches[1] == '') {
                        array_pop($arrFragments);
                    } // else set alias
                    else {
                        $arrFragments[count($arrFragments) - 1] = $alias;
                    }

                    array_push($arrFragments, 'language', $language);
                }
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
            ->execute(is_numeric($arrFragments[0] ? $arrFragments[0] : 0), $language, $arrFragments[0], $language);

        if ($objAlias->numRows) {
            $arrFragments[0] = $objAlias->alias;
        }


        // Add the second fragment as auto_item if the number of fragments is even
        if ($GLOBALS['TL_CONFIG']['useAutoItem'] && count($arrFragments) % 2 == 0)
        {
            array_insert($arrFragments, 1, array('auto_item'));
        }

        return $arrFragments;
    }

/**
 *TODO if needed
 * function getRootPageFromUrl(){
 *   error_log( __METHOD__.':'.var_export($_GET,true) );
 *   return;
 * }
 */

}//end class
