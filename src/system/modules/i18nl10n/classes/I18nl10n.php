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
 * @package     i18nl10n classes
 * @version     1.2.1
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
     * Known and unsupported Contao modules
     *
     * @var Array
     */
    protected $unsupportedModules = array('news', 'calendar');

    /**
     * Class instance
     *
     * @var I18nl10n
     */
    protected static $instance = null;

    /**
     * Current time
     *
     * @var Integer
     */
    private $time;

    /**
     * Set default values
     */
    function __construct()
    {
        $this->time = time();
    }

    /**
     * Create instance of i18nl10n class
     *
     * @return I18nl10n
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new I18nl10n();
        }

        return static::$instance;
    }

    /**
     * Get unsupported Contao modules
     *
     * @return Array
     */
    public function getUnsupportedModules()
    {
        return $this->unsupportedModules;
    }

    /**
     * Get first published sub page for given l10n id and language
     *
     * @param   Integer $intId
     * @param   String  $strLang
     *
     * @return array|false
     */
    public function findFirstPublishedL10nRegularPageByPid($intId, $strLang)
    {
        $sqlPublishedCondition = BE_USER_LOGGED_IN
            ? ''
            : " AND (start='' OR start < {$this->time}) AND (stop='' OR stop > {$this->time}) AND published = 1 ";

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
     * @param   Integer $intId
     * @param   String  $strLang
     *
     * @return \Contao\Page|null
     */
    public function findL10nWithDetails($intId, $strLang)
    {
        // Get page by id
        $objCurrentPage = \PageModel::findWithDetails($intId);

        // Get localization
        return $this->findPublishedL10nPage($objCurrentPage, $strLang, false);
    }

    /**
     * Find localized page for given page object and replace string values
     *
     * @param \PageModel    $objPage
     * @param bool          [$blnTranslateOnly]
     *
     * @return object|null
     */
    public function findPublishedL10nPage($objPage, $blnTranslateOnly = true)
    {
        //get language specific page properties
        $fields = array('title', 'language', 'pageTitle', 'description', 'url', 'cssClass', 'dateFormat', 'timeFormat', 'datimFormat', 'start', 'stop');

        if (!$blnTranslateOnly) {
            $fields = array_merge($fields, array('id', 'pid', 'sorting', 'tstamp', 'alias', 'i18nl10n_published'));
        }

        $sqlPublishedCondition = BE_USER_LOGGED_IN
            ? '' :
            "AND (start='' OR start < {$this->time}) AND (stop='' OR stop > {$this->time}) AND i18nl10n_published = 1";

        $sql = 'SELECT ' . implode(',', $fields) . " FROM tl_page_i18nl10n WHERE pid = ? AND language = ? $sqlPublishedCondition";

        $objL10nPage = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($objPage->id, $GLOBALS['TL_LANGUAGE']);

        // If fallback and localization are not published, return null
        if (!$objPage->i18nl10n_published && !$objL10nPage->count()) {
            return null;
        }

        if ($objL10nPage->first()) {
            // Replace strings with localized content
            foreach ($fields as $field) {
                if ($objL10nPage->$field) {
                    $objPage->$field = $objL10nPage->$field;
                }
            }
        } else {
            // else at least keep current language to prevent language change and set flag
            $objPage->language            = $GLOBALS['TL_LANGUAGE'];
            $objPage->useFallbackLanguage = true;
        }

        // update root information
        $objL10nRootPage = $this->getL10nRootPage($objPage);

        if ($objL10nRootPage) {
            $objPage->rootTitle = $objL10nRootPage->title;

            if ($objPage->pid == $objPage->rootId) {
                $objPage->parentTitle     = $objL10nRootPage->title;
                $objPage->parentPageTitle = $objL10nRootPage->pageTitle;
            }
        }

        return $objPage;
    }

    /**
     * Get language definition for given page id and table
     *
     * @param   Integer     $intId
     * @param   String      $strTable
     * @param   Boolean     [$blnForCurrentUserOnly]  Get only languages for current BE users permission
     *
     * @return array
     */
    public function getLanguagesByPageId($intId, $strTable, $blnForCurrentUserOnly = false)
    {
        $intId = intval($intId);

        if ( in_array($strTable, array('tl_page_i18nl10n', 'tl_page')) ) {
            $rootId = $this->getRootIdByPageId($intId, $strTable);

            return $this->getLanguagesByRootId($rootId, $blnForCurrentUserOnly);
        }

        return array();
    }

    /**
     * Get root page by page id and table
     *
     * @param   Integer     $intId
     * @param   Integer     $strTable
     *
     * @return mixed|null
     */
    public function getRootIdByPageId($intId, $strTable)
    {
        switch ($strTable) {
            case 'tl_page':
                return \PageModel::findWithDetails($intId)->rootId;

            case 'tl_page_i18nl10n':
                $arrPage = \Database::getInstance()
                    ->prepare('SELECT * FROM tl_page_i18nl10n WHERE id = ?')
                    ->execute($intId)
                    ->fetchAssoc();

                return \PageModel::findWithDetails($arrPage['pid'])->rootId;
        }

        return null;
    }

    /**
     * Get language definition by root page ID
     *
     * @param   Integer $intId
     * @param   Boolean $blnForCurrentUserOnly  Get only languages based on current be user permissions
     *
     * @return array
     */
    public function getLanguagesByRootId($intId, $blnForCurrentUserOnly = false)
    {
        /** @var \Database\Mysqli\Result $objRootPage */
        $objRootPage = \Database::getInstance()
            ->prepare('SELECT * FROM tl_page WHERE id = ?')
            ->execute($intId);

        $arrLanguages = $this->mapLanguagesFromDatabaseRootPageResult($objRootPage, $blnForCurrentUserOnly);

        return array_shift($arrLanguages);
    }

    /**
     * Get languages by given or actual domain
     *
     * @param   String  [$strDomain]
     *
     * @return array
     */
    public function getLanguagesByDomain($strDomain = null)
    {
        /** @var \Database\Mysqli\Result $objRootPage */
        $objRootPage = $this->getRootPageByDomain($strDomain);

        $arrLanguages = $this->mapLanguagesFromDatabaseRootPageResult($objRootPage);

        return array_shift($arrLanguages);
    }

    /**
     * Get all available languages
     *
     * @param bool [$blnForCurrentUserOnly]  Only languages for current logged in user will be returned
     * @param bool [$blnReturnFlat]         Return a flat language array
     *
     * @return array
     */
    public function getAvailableLanguages($blnForCurrentUserOnly = false, $blnReturnFlat = false)
    {
        // Get root pages
        $objRootPages = $this->getAllRootPages();

        return $this->mapLanguagesFromDatabaseRootPageResult($objRootPages, $blnForCurrentUserOnly, $blnReturnFlat);
    }

    /**
     * Get all root pages for current Contao setup
     *
     * @return \Database\Result
     */
    public function getAllRootPages()
    {
        return \Database::getInstance()->query('SELECT * FROM tl_page WHERE type = "root" AND tstamp > 0');
    }

    /**
     * Get a root page by given or actual domain
     *
     * @param String    [$strDomain]    Default: null
     *
     * @return \Database\Result
     */
    public function getRootPageByDomain($strDomain = null)
    {
        if (empty($strDomain)) {
            $strDomain = \Environment::get('host');
        }

        // Find page with related or global DNS
        return \Database::getInstance()
            ->prepare('
            (SELECT * FROM tl_page WHERE type = "root" AND dns = ?)
            UNION
            (SELECT * FROM tl_page WHERE type = "root" AND dns = "")')
            ->limit(1)
            ->execute($strDomain);
    }

    /**
     * Get localized root page by page object
     *
     * @param $objPage
     *
     * @return \Database\Result|null
     */
    public function getL10nRootPage($objPage)
    {
        $sqlPublishedCondition = BE_USER_LOGGED_IN
            ? ''
            : " AND (start = '' OR start < {$this->time}) AND (stop = '' OR stop > {$this->time}) AND i18nl10n_published = 1 ";

        $sql = 'SELECT title FROM tl_page_i18nl10n WHERE pid = ? AND language = ? ' . $sqlPublishedCondition;

        $objL10nRootPage = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($objPage->rootId, $GLOBALS['TL_LANGUAGE']);

        return $objL10nRootPage->row() ? $objL10nRootPage : null;
    }

    /**
     * Get native language names
     *
     * @return array
     */
    public function getNativeLanguageNames()
    {
        // Var name defined by languages.php (Don't change!)
        $langsNative = array();

        // Include languages to get $langsNative
        include(TL_ROOT . '/system/config/languages.php');

        return $langsNative;
    }

    /**
     * Map all default and localized languages from a database result and return as array
     *
     * @param \Database\Mysqli\Result $objRootPage
     * @param bool                    [$blnForCurrentUserOnly]    Will only return languages for which the current user has permissions
     * @param bool                    [$blnReturnFlat]          Return a flat array with all languages
     *
     * @return array
     */
    private function mapLanguagesFromDatabaseRootPageResult(\Database\Mysqli\Result $objRootPage, $blnForCurrentUserOnly = false, $blnReturnFlat = false)
    {
        $arrLanguages = array();

        if ( $objRootPage->count() ) {

            if ($blnReturnFlat) {
                // Loop domains
                while ($objRootPage->next()) {
                    if (!$blnForCurrentUserOnly || $this->userHasLanguagePermission($objRootPage->id, '*')) {
                        $arrLanguages[] = $objRootPage->language;
                    }

                    // Loop localizations
                    foreach ((array) deserialize($objRootPage->i18nl10n_localizations) as $localization) {
                        if (!empty($localization['language'])) {
                            if (!$blnForCurrentUserOnly || $this->userHasLanguagePermission($objRootPage->id, $localization['language'])) {
                                $arrLanguages[] = $localization['language'];
                            }
                        }
                    }
                }

                // Make entries unique and sort
                $arrLanguages = array_unique($arrLanguages);
                asort($arrLanguages);

            } else {
                // Loop root pages and collect languages
                while ($objRootPage->next()) {

                    $strDns = $objRootPage->dns ?: '*';

                    $arrLanguages[$strDns] = array
                    (
                        'rootId'        => $objRootPage->id,
                        'default'       => $objRootPage->language,
                        'localizations' => array(),
                        'languages'     => array()
                    );

                    if (!$blnForCurrentUserOnly || $this->userHasLanguagePermission($objRootPage->id, '*')) {
                        $arrLanguages[$strDns]['languages'][] = $objRootPage->language;
                    }

                    foreach ((array) deserialize($objRootPage->i18nl10n_localizations) as $localization) {

                        if (!empty($localization['language'])) {
                            if (!$blnForCurrentUserOnly
                                || $this->userHasLanguagePermission(
                                    $objRootPage->id,
                                    $localization['language']
                                )
                            ) {
                                $arrLanguages[$strDns]['localizations'][] = $localization['language'];
                                $arrLanguages[$strDns]['languages'][]     = $localization['language'];
                            }
                        }
                    }

                    // Sort alphabetically
                    asort($arrLanguages[$strDns]['localizations']);
                    asort($arrLanguages[$strDns]['languages']);
                }
            }
        }

        return $arrLanguages;
    }

    /**
     * Count available root pages
     *
     * @return int
     */
    public function countRootPages()
    {
        $objRootPages = $this->getAllRootPages();

        return $objRootPages->count();
    }

    /**
     * Get language options for user and group permission
     *
     * @return array
     */
    public function getLanguageOptionsForUserOrGroup()
    {
        return $this->mapLanguageOptionsForUserOrGroup($this->getAvailableLanguages());
    }

    /**
     * Create domain related language array for user and group permission
     *
     * @param   Array   $arrLanguages
     *
     * @return array
     */
    private function mapLanguageOptionsForUserOrGroup(array $arrLanguages) {
        $arrMappedLanguages = array();

        // Loop Domains
        foreach ($arrLanguages as $domain => $config) {

            // Create Domain identifier
            $arrDomainLanguages = array(
                $config['rootId'] . '::*' => ''
            );

            // Loop languages
            foreach ($config['languages'] as $language) {
                // Create unique key by combining root id and language
                $strKey = $config['rootId'] . '::' . $language;

                // Add rootId to make unique
                $arrDomainLanguages[$strKey] = $language;
            }

            $arrMappedLanguages[$domain] = $arrDomainLanguages;
        }

        return $arrMappedLanguages;
    }

    /**
     * Check if a user has permission to handle given language by root id
     *
     * @param   Integer $intRootPageId
     * @param   String  $strLanguage
     *
     * @return bool
     */
    private function userHasLanguagePermission($intRootPageId, $strLanguage)
    {
        $arrUserData = \BackendUser::getInstance()->getData();

        return intval($arrUserData['admin']) === 1 || in_array($intRootPageId . '::' . $strLanguage, (array) $arrUserData['i18nl10n_languages']);
    }
}
