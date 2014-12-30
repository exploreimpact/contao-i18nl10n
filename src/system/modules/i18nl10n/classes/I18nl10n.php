<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   Verstärker, Patric Eberle 2014
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace Verstaerker\I18nl10n\Classes;


/**
 * Class I18nl10n
 *
 * Global Functions for i18nl10n module
 *
 * @package Verstaerker\I18nl10n\Classes
 */
class I18nl10n extends \Controller
{
    /**
     * vsprintf with array argument support
     *
     * @param $format
     * @param array $data
     * @return string
     */
    public static function vnsprintf($format, array $data)
    {
        preg_match_all(
            '/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x',
            $format,
            $match,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        $offset = 0;
        $keys = array_keys($data);

        foreach ($match as &$value)
        {
            if (($key = array_search($value[1][0], $keys, TRUE)) !== FALSE || (is_numeric($value[1][0]) && ($key = array_search((int)$value[1][0], $keys, TRUE)) !== FALSE))
            {
                $len = strlen($value[1][0]);
                $format = substr_replace($format, 1 + $key, $offset + $value[1][1], $len);
                $offset -= $len - strlen(1 + $key);
            }
        }

        return vsprintf($format, $data);
    }

    /**
     * Find alias for internationalized content or use fallback language alias
     *
     * @param $arrFragments
     * @param $strLanguage
     * @return null|array
     */
    public static function findAliasByLocalizedAliases($arrFragments, $strLanguage)
    {

        $arrAlias = array();
        $arrAliasGuess = array();
        $strAlias = $arrFragments[0];
        $dataBase = \Database::getInstance();

        if (\Config::get('folderUrl') && $arrFragments[count($arrFragments)-2] == 'language')
        {
            // glue together possible aliases
            for($i = 0; count($arrFragments)-2 > $i; $i++)
            {
                $arrAliasGuess[] = ($i == 0)
                    ? $arrFragments[$i]
                    : $arrAliasGuess[$i-1] . '/' . $arrFragments[$i];
            }

            // Remove everything that is not an alias
            $arrAliasGuess = array_filter(array_map(function($v)
            {
                return preg_match('/^[\pN\pL\/\._-]+$/u', $v) ? $v : null;
            }, $arrAliasGuess));

            // reverse array to get specific entries first
            $arrAliasGuess = array_reverse($arrAliasGuess);

            $strAlias = implode("','", $arrAliasGuess);
        }

        // Try to find a localized content
        $sql = "SELECT pid, alias
                FROM tl_page_i18nl10n
                WHERE
                  id = ? AND language = ?
                  OR alias IN('" . $strAlias . "') AND language = ?
                ORDER BY " . $dataBase->findInSet('alias', $arrAliasGuess) . " LIMIT 0,1";

        $arrL10nItem = $dataBase
            ->prepare($sql)
            ->execute(
                is_numeric($arrFragments[0]) ? $arrFragments[0] : 0,
                $strLanguage,
                $strLanguage
            )
            ->fetchAssoc();

        // Set l10n alias, if item was found (is needed to be removed from url params later on)
        if (!empty($arrL10nItem))
        {
            $arrAlias['l10nAlias'] = $arrL10nItem['alias'];
        }

        // Try to find default language page by localized id or alias
        $sql = "
            SELECT alias
            FROM tl_page
            WHERE
                (
                    id = ?
                    OR alias IN('" . $strAlias . "')
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

        $sql .= 'ORDER BY ' . $dataBase->findInSet('alias', $arrAliasGuess);

        $objL10n = $dataBase
            ->prepare($sql)
            ->execute( empty($arrL10nItem) ? 0 : $arrL10nItem['pid'] );

        // Set alias if a page was found
        if ($objL10n !== null)
        {
            // best match is in first item
            $arrPage = $objL10n->row();
            $arrAlias['alias'] = $arrPage['alias'];
        }

        return $arrAlias;

    }

    /**
     * Get first published sub page for given l10n id and language
     *
     * @param $intId
     * @param $strLang
     *
     * @return array|false
     */
    public static function findFirstPublishedL10nRegularPageByPid($intId, $strLang)
    {
        $time = time();
        $sqlPublishedCondition = !BE_USER_LOGGED_IN
            ? " AND (start='' OR start < $time) AND (stop='' OR stop > $time) AND published = 1 "
            : '';

        $sql = "
            SELECT *
            FROM tl_page_i18nl10n
            WHERE
              pid = (
                SELECT id
                FROM tl_page
                WHERE
                  pid = ?
                  AND type = 'regular'
                  $sqlPublishedCondition
                ORDER BY sorting
                LIMIT 0,1
              )
            AND language = ?";

        $request = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($intId, $strLang);

        return $request->fetchAssoc();
    }

    /**
     * Get first published sub page for given l10n id and language
     *
     * @param $intId
     * @param $strLang
     *
     * @return object
     */
    public static function findL10nWithDetails($intId, $strLang)
    {
        // Get page by id
        $objCurrentPage = \PageModel::findWithDetails($intId);

        // Get localization
        return I18nl10n::findPublishedL10nPage($objCurrentPage, $strLang, false);
    }

    /**
     * Find localized page for given page obj and replace string values
     *
     * @param       $objPage
     * @param       $strLang
     * @param bool  $blnTranslateOnly
     *
     * @return object
     */
    public static function findPublishedL10nPage($objPage, $strLang, $blnTranslateOnly = true)
    {
        //get language specific page properties
        $time = time();
        $fields = 'title,language,pageTitle,description,url,cssClass,dateFormat,timeFormat,datimFormat,start,stop';

        if (!$blnTranslateOnly)
        {
            $fields .= ',id,pid,sorting,tstamp,alias,l10n_published';
        }

        $sqlPublishedCondition = !BE_USER_LOGGED_IN
            ? " AND (start='' OR start < $time) AND (stop='' OR stop > $time) AND l10n_published = 1 "
            : '';

        $sql = "SELECT $fields FROM tl_page_i18nl10n WHERE pid = ? AND language = ? $sqlPublishedCondition";

        $objL10nPage = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($objPage->id, $strLang);

        if ($objL10nPage->numRows)
        {
            $objPage->defaultPageTitle = $objPage->pageTitle;
            $objPage->defaultTitle = $objPage->title;

            // Replace strings with localized content
            foreach (explode(',', $fields) as $field)
            {
                if ($objL10nPage->$field)
                {
                    $objPage->$field = $objL10nPage->$field;
                }
            }
        }

        return $objPage;
    }

    /**
     * Get language definition for given page id and table
     *
     * @param      $intId
     * @param      $strTable
     * @param bool $blnIncludeDefault   Include default language
     *
     * @return array
     */
    static public function getLanguagesByPageId($intId, $strTable, $blnIncludeDefault = true)
    {
        switch ($strTable) {
            case 'tl_page':
                $rootId = self::getRootIdByPageId($intId, $strTable);

                $arrLanguages = self::getLanguagesByRootId($rootId, $blnIncludeDefault);
                break;

            case 'tl_page_i18nl10n':
                $rootId = self::getRootIdByPageId($intId, $strTable);

                $arrLanguages = self::getLanguagesByRootId($rootId, $blnIncludeDefault);
                break;

            default:
                $arrLanguages = array();
                break;
        }

        return $arrLanguages;
    }

    /**
     * Get root page by page id and table
     *
     * @param $intId
     * @param $strTable
     *
     * @return mixed|null
     */
    static public function getRootIdByPageId($intId, $strTable)
    {
        switch ($strTable) {
            case 'tl_page':
                $rootId = \PageModel::findWithDetails($intId)->rootId;
                break;

            case 'tl_page_i18nl10n':
                $arrPage = \Database::getInstance()
                    ->prepare('SELECT * FROM tl_page_i18nl10n WHERE id = ?')
                    ->execute($intId)
                    ->fetchAssoc();

                $rootId = \PageModel::findWithDetails($arrPage['pid'])->rootId;
                break;

            default:
                $rootId = null;
                break;
        }

        return $rootId;
    }

    /**
     * Get language definition by root page id
     *
     * @param      $intId
     * @param bool $blnIncludeDefault
     *
     * @return array
     */
    static public function getLanguagesByRootId($intId, $blnIncludeDefault = true)
    {
        $objPage = \PageModel::findWithDetails($intId);
        $arrI18nl10nLanguages = array();

        // Get language values
        if ($objPage) {
            foreach (deserialize($objPage->i18nl10n_languages) as $entry) {
                if (!empty($entry['language'])) {
                    $arrI18nl10nLanguages[] = $entry['language'];
                }
            }

            // Include default language
            if ($blnIncludeDefault) {
                $arrI18nl10nLanguages[] = $objPage->language;
            }
        }

        return $arrI18nl10nLanguages;
    }

    /**
     * Get all available languages
     *
     * @param bool $blnIncludeDefault
     *
     * @return array
     */
    static public function getAllLanguages($blnIncludeDefault = true, $blnSortByRoot = false)
    {
        // Get root pages
        $objRootPages = self::getAllRootPages();

        $arrLanguages = array();

        if ($objRootPages->numRows) {
            // Loop root pages and collect languages
            while ($objRootPages->next()) {

                if ($blnSortByRoot) {
                    $arrLanguages[$objRootPages->id] = array();
                }

                foreach (deserialize($objRootPages->i18nl10n_languages) as $entry) {
                    if (!empty($entry['language'])) {
                        // If sort by root, add additional level
                        if ($blnSortByRoot) {
                            $arrLanguages[$objRootPages->id][] = $entry['language'];
                        } else {
                            $arrLanguages[] = $entry['language'];
                        }
                    }
                }

                if ($blnIncludeDefault) {

                    // If sort by root add additional level
                    if ($blnSortByRoot) {
                        $arrLanguages[$objRootPages->id][] = $objRootPages->language;
                    } else {
                        $arrLanguages[] = $objRootPages->language;
                    }
                }

                // Remove duplicates
                if($blnSortByRoot) {
                    $arrLanguages[$objRootPages->id] = array_unique($arrLanguages[$objRootPages->id]);
                }
            }
        }

        // Remove duplicates
        if(!$blnSortByRoot) {
            $arrLanguages = array_unique($arrLanguages);
        }

        return $arrLanguages;
    }

    /**
     * Get all root pages for current Contao setup
     *
     * @return \Database\Result
     */
    static public function getAllRootPages()
    {
        return \Database::getInstance()->query('SELECT * FROM tl_page WHERE type = "root"');
    }

    /**
     * Get native language names
     *
     * @return array
     */
    static public function getNativeLanguageNames()
    {
        $langsNative = array();

        // Include languages to get $langsNative
        include(TL_ROOT . '/system/config/languages.php');

        return $langsNative;
    }
}
