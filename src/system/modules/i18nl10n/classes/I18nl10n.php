<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   2015 Verstärker, Patric Eberle
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
     * @param       $format
     * @param array $data
     *
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
        $keys   = array_keys($data);

        foreach ($match as &$value) {
            if (($key = array_search($value[1][0], $keys, true)) !== false || (is_numeric($value[1][0]) && ($key =
                        array_search((int) $value[1][0], $keys, true)) !== false)
            ) {
                $len    = strlen($value[1][0]);
                $format = substr_replace($format, 1 + $key, $offset + $value[1][1], $len);
                $offset -= $len - strlen(1 + $key);
            }
        }

        return vsprintf($format, $data);
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
        $time                  = time();
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
     * Find localized page for given page object and replace string values
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
        $time   = time();
        $fields = 'title,language,pageTitle,description,url,cssClass,dateFormat,timeFormat,datimFormat,start,stop';

        if (!$blnTranslateOnly) {
            $fields .= ',id,pid,sorting,tstamp,alias,i18nl10n_published';
        }

        $sqlPublishedCondition = !BE_USER_LOGGED_IN
            ? " AND (start='' OR start < $time) AND (stop='' OR stop > $time) AND i18nl10n_published = 1 "
            : '';

        $sql = "SELECT $fields FROM tl_page_i18nl10n WHERE pid = ? AND language = ? $sqlPublishedCondition";

        $objL10nPage = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($objPage->id, $strLang);

        if ($objL10nPage->numRows) {
            $objPage->defaultPageTitle = $objPage->pageTitle;
            $objPage->defaultTitle     = $objPage->title;

            // Replace strings with localized content
            foreach (explode(',', $fields) as $field) {
                if ($objL10nPage->$field) {
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
     * @param bool $blnIncludeDefault Include default language
     *
     * @return array
     */
    static public function getLanguagesByPageId($intId, $strTable, $blnIncludeDefault = true)
    {
        switch ($strTable) {
            case 'tl_page_i18nl10n':
                // no break

            case 'tl_page':
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
        $objRootPage = \Database::getInstance()
            ->prepare('SELECT * FROM tl_page WHERE id = ?')
            ->execute($intId);

        $arrLanguage = self::mapLanguagesFromDatabaseRootPageResult($objRootPage);

        return array_shift($arrLanguage);
    }

    /**
     * Get languages by given or actual domain
     *
     * @param null $strDomain
     *
     * @return array
     */
    static public function getLanguagesByDomain($strDomain = null)
    {
        $objRootPage = self::getRootPageByDomain($strDomain);

        $arrLanguage = self::mapLanguagesFromDatabaseRootPageResult($objRootPage);

        return array_shift($arrLanguage);
    }

    /**
     * Get all available languages
     *
     * @return array
     */
    static public function getAllLanguages()
    {
        // Get root pages
        $objRootPages = self::getAllRootPages();

        return self::mapLanguagesFromDatabaseRootPageResult($objRootPages);
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
     * Get a root page by given or actual domain
     *
     * @param null $strDomain
     *
     * @return \Database\Result
     */
    static public function getRootPageByDomain($strDomain = null)
    {
        if (empty($strDomain)) {
            $strDomain = \Environment::get('host');
        }

        return \Database::getInstance()
            ->prepare('SELECT * FROM tl_page WHERE dns = ?')
            ->limit(1)
            ->execute($strDomain);
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

    /**
     * Map all default and localized languages from a database result and return as array
     *
     * @param \Database\Mysqli\Result $objRootPage
     *
     * @return array
     */
    static private function mapLanguagesFromDatabaseRootPageResult(\Database\Mysqli\Result $objRootPage)
    {
        $arrLanguages = array();

        if ($objRootPage->count()) {
            // Loop root pages and collect languages
            while ($objRootPage->next()) {
                $arrLanguages[$objRootPage->dns] = array
                (
                    'rootId'        => $objRootPage->id,
                    'default'       => $objRootPage->language,
                    'localizations' => array(),
                    'languages'     => array($objRootPage->language)
                );

                foreach (deserialize($objRootPage->i18nl10n_localizations) as $localization) {

                    if (!empty($localization['language'])) {
                        $arrLanguages[$objRootPage->dns]['localizations'][] = $localization['language'];
                        $arrLanguages[$objRootPage->dns]['languages'][]     = $localization['language'];
                    }
                }

                // @todo: check on save if duplicated entries
                $arrLanguages[$objRootPage->dns]['localizations'] =
                    array_unique($arrLanguages[$objRootPage->dns]['localizations']);
                $arrLanguages[$objRootPage->dns]['languages']     =
                    array_unique($arrLanguages[$objRootPage->dns]['languages']);

                // Sort alphabetically
                asort($arrLanguages[$objRootPage->dns]['localizations']);
                asort($arrLanguages[$objRootPage->dns]['languages']);
            }
        }

        return $arrLanguages;
    }

    /**
     * Count available root pages
     *
     * @return int
     */
    static public function countRootPages()
    {
        $objRootPages = I18nl10n::getAllRootPages();

        return $objRootPages->count();
    }
}
