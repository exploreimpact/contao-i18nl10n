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
 *
 * @package Verstaerker\I18nl10n\Classes
 */
class I18nl10nHooks extends \System
{
    /**
     * Generates url for the site according to settings from the backend
     *
     * Assumptions:
     * \Config::get('disableAlias') == false;
     *
     * @param array $arrRow
     * @param string $strParams
     * @param string $strUrl
     * @return string
     * @throws \Exception
     */
    public function generateFrontendUrl($arrRow, $strParams, $strUrl)
    {
        if (!is_array($arrRow))
        {
            throw new \Exception('not an associative array.');
        }

        $language = (array_key_exists('robots', $arrRow) ? $GLOBALS['TL_LANGUAGE'] : $arrRow['language']);

        $alias = $arrRow['alias'];
        // regex to remove auto_item and language
        $regex = '@/auto_item|/language/[A-z]{2}|[\?&]language=[A-z]{2}@';

        // remove auto_item and language
        $strParams = preg_replace($regex, '', $strParams);
        $strUrl = preg_replace($regex, '', $strUrl);

        // if alias is disabled add language to get param end return
        if (\Config::get('disableAlias'))
        {
            $missingValueRegex = '@(.*\?[^&]*&)([^&]*)=(?=$|&)(&.*)?@';

            if (\Config::get('useAutoItem') && preg_match($missingValueRegex, $strUrl) == 1)
            {
                $strUrl = preg_replace($missingValueRegex, '${1}auto_item=${2}${3}', $strUrl);
            }

            return $strUrl . '&language=' . $language;
        }

        if (\Config::get('i18nl10n_alias_suffix') && !\Config::get('disableAlias'))
        {
            $strL10nUrl = $alias . $strParams . '.' . $language . \Config::get('urlSuffix');

            // if rewrite is off, add environment
            if (!\Config::get('rewriteURL'))
            {
                $strL10nUrl = 'index.php/' . $strL10nUrl;
            }
        }
        elseif (\Config::get('i18nl10n_addLanguageToUrl'))
        {

            $strL10nUrl = $language . '/' . $alias . $strParams . \Config::get('urlSuffix');

            // if rewrite is off, add environment
            if (!\Config::get('rewriteURL'))
            {
                $strL10nUrl = 'index.php/' . $strL10nUrl;
            }

            // if alias is missing (f.ex. index.html), add it (exclude news!)
            // search for
            // www.domain.com/
            // www.domain.com/foo/
            if (!\Config::get('disableAlias')
                && preg_match('@' . $arrRow['alias'] . '(?=\\' . \Config::get('urlSuffix') . '|/)@', $strL10nUrl) === false
            )
            {
                $strL10nUrl .= $alias . \Config::get('urlSuffix');
            }

        }
        else
        {
            // if get variables
            if (strpos($strUrl, '?') !== false)
            {
                // if variable 'language' replace it
                if (strpos($strUrl, 'language=') !== false)
                {
                    $regex = "@language=[A-z]{2}@";
                    $strL10nUrl = preg_replace(
                        $regex, 'language=' . $language, $strUrl
                    );
                } // if no variable 'language' add it
                else
                {
                    $strL10nUrl = $strUrl . '&language=' . $language;
                }
            } // if no variables define variable 'language'
            else
            {
                $strL10nUrl = $strUrl . '?language=' . $language;
            }
        }

        return $strL10nUrl;
    }


    /**
     * Get page id from url, based on current contao settings
     *
     * @param array $arrFragments
     * @return array
     */
    public function getPageIdFromUrl(Array $arrFragments)
    {
        global $TL_CONFIG;

        $arrFragments = array_map('urldecode', $arrFragments);
        $languages = deserialize($TL_CONFIG['i18nl10n_languages']);
        $language = $TL_CONFIG['i18nl10n_default_language'];

        // strip auto_item
        if (\Config::get('useAutoItem') && $arrFragments[1] == 'auto_item')
        {
            $arrFragments = array_delete($arrFragments, 1);
        }

        // try to get language by i18nl10n URL
        if ($TL_CONFIG['i18nl10n_addLanguageToUrl'])
        {
            if (preg_match('@^([A-z]{2})$@', $arrFragments[0], $matches))
            {
                $language = strtolower($matches[1]);

                // remove old language entry
                $arrFragments = array_delete($arrFragments, 0);

                // append new language entry
                array_push($arrFragments, 'language', $language);
            }
        } // try to get language by suffix
        elseif ($TL_CONFIG['i18nl10n_alias_suffix'] && !\Config::get('disableAlias'))
        {
            // last element should contain language info
            if (preg_match('@^([_\-\pL\pN\.]*(?=\.))?\.?([A-z]{2})$@u', $arrFragments[count($arrFragments) - 1], $matches))
            {

                // define language and alias value
                $language = strtolower($matches[2]);
                $alias = $matches[1] != '' ? $matches[1] : $arrFragments[count($arrFragments) - 1];

                // if only language was found, pop it from array
                if ($matches[1] == '')
                {
                    array_pop($arrFragments);
                } // else set alias
                else
                {
                    $arrFragments[count($arrFragments) - 1] = $alias;
                }

                array_push($arrFragments, 'language', $language);
            }
        }
        elseif (\Input::get('language'))
        {
            $language = \Input::get('language');
        }

        $sql = "
            SELECT
                alias
            FROM
                tl_page
            WHERE
                (
                    id = (SELECT pid FROM tl_page_i18nl10n WHERE id = ? AND language = ?)
                    OR id = (SELECT pid FROM tl_page_i18nl10n WHERE alias = ? AND language = ?)
                )
        ";

        if (!BE_USER_LOGGED_IN)
        {
            $time = time();
            $sql .= "
                AND (start = '' OR start < $time)
                AND (stop = '' OR stop > $time)
                AND published = 1
            ";
        }

        // TODO: check if there is a better solution then limit() for multiple alias use
        $arrAlias = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute(is_numeric($arrFragments[0] ? : 0), $language, $arrFragments[0], $language)
            ->fetchAssoc();

        if (is_array($arrAlias) && !empty($arrAlias)) $arrFragments[0] = $arrAlias['alias'];

        // Add the second fragment as auto_item if the number of fragments is even
        if (\Config::get('useAutoItem') && count($arrFragments) % 2 == 0)
        {
            array_insert($arrFragments, 1, array('auto_item'));
        }

        return $arrFragments;
    }


    /**
     * Only make elements visible, that belong to this or all languages
     *
     * @param $objElement
     * @param $blnIsVisible
     * @return mixed
     */
    public function isVisibleElement($objElement, $blnIsVisible)
    {

        global $objPage;

        if ($blnIsVisible && $objElement->language)
        {

            // check if given language is valid of fallback should be used
            $strLanguage = $objPage->useFallbackLanguage ? \Config::get('i18nl10n_default_language') : $GLOBALS['TL_LANGUAGE'];

            $blnIsVisible = $objElement->language == $strLanguage;
        }

        return $blnIsVisible;
    }


    /**
     * Breadcrumb hook to translate elements
     *
     * @param $arrItems Array
     * @param $objModule \Module
     * @return Array
     */
    public function generateBreadcrumb($arrItems, \Module $objModule)
    {
        $arrPages = array();

        foreach ($arrItems as $item)
        {
            $arrPages[] = $item['isRoot'] ? $item['data']['pid'] : $item['data']['id'];
        }

        $sql = "
            SELECT
              *
            FROM
              tl_page_i18nl10n
            WHERE
              pid IN (" . implode(',', $arrPages) . ")
              AND language = ?
        ";

        if (!BE_USER_LOGGED_IN)
        {
            $time = time();
            $sql .= "
                AND (start = '' OR start < $time)
                AND (stop = '' OR stop > $time)
                AND l10n_published = 1
            ";
        }

        $arrL10n = \Database::getInstance()
            ->prepare($sql)
            ->execute($GLOBALS['TL_LANGUAGE'])
            ->fetchAllassoc();

        // if translated page, replace given fields in element array
        if (count($arrL10n) > 0)
        {
            // each breadcrumb element
            for ($i = 0; count($arrItems) > $i; $i++)
            {
                // each translation
                foreach ($arrL10n as $l10n)
                {
                    // if translation for actual breadcrumb element
                    if ($arrItems[$i]['isRoot'] && $arrItems[$i]['data']['pid'] == $l10n['pid']
                        || !$arrItems[$i]['isRoot'] && $arrItems[$i]['data']['id'] == $l10n['pid']
                    )
                    {
                        if ($l10n['pageTitle']) $arrItems[$i]['title'] = $l10n['pageTitle'];
                        if ($l10n['title']) $arrItems[$i]['link'] = $l10n['title'];
                        break;
                    }
                }
            }
        }

        return $arrItems;
    }

}
