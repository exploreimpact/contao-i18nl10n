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
     * Shared text columns of tl_page and tl_page_i18nl10n
     *
     * @var array
     */
    protected $textTableFields = array('title', 'language', 'pageTitle', 'description', 'url', 'cssClass', 'dateFormat', 'timeFormat', 'datimFormat', 'start', 'stop');

    /**
     * Shared meta data columns of tl_page and tl_page_i18nl10n
     *
     * @var array
     */
    protected $metaTableFields = array('id', 'pid', 'sorting', 'tstamp', 'alias', 'i18nl10n_published');

    /**
     * Initialize class
     */
    function __construct()
    {
        $this->time = time();

        // Import database handler
        $this->import('Database');
    }

    /**
     * Get table columns
     *
     * @param $blnIncludeMeta   boolean     Include meta data fields
     *
     * @return string|array
     */
    public function getTableFields($blnIncludeMeta = false)
    {
        // Get language specific page properties
        $fields = $this->textTableFields;

        if ($blnIncludeMeta) {
            $fields = array_merge($fields, $this->metaTableFields);
        }

        return $fields;
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
     * @return \Contao\PageModel|null
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
     * @param String        [$strLang]              Search for a specific language
     * @param bool          [$blnTranslateOnly]     Get only translation. If false meta data will also be modified.
     *
     * @return object|null
     */
    public function findPublishedL10nPage($objPage, $strLang = null, $blnReplaceMetaFields = false)
    {
        // If no page alias is defined, don't continue
        if (empty($objPage->mainAlias)) {
            return $objPage;
        }

        // Get to be replaced fields
        $fields = $this->getTableFields($blnReplaceMetaFields);

        $sqlPublishedCondition = BE_USER_LOGGED_IN
            ? '' :
            "AND (start='' OR start < {$this->time}) AND (stop='' OR stop > {$this->time}) AND i18nl10n_published = 1";

        // Add identification fields and combine sql
        $sql = '
            SELECT
                pid AS l10nPid,
                alias AS l10nAlias, '
                . implode(',', $fields) . "
            FROM
                tl_page_i18nl10n
            WHERE
                pid IN(?,?,?)
                AND language = ? $sqlPublishedCondition
            ORDER BY "
               . $this->Database->findInSet('pid', array($objPage->id, $objPage->pid, $objPage->rootId))
        ;

        // Fetch related pages
        $arrL10nRelatedPages = $this->Database
            ->prepare($sql)
            ->execute($objPage->id, $objPage->pid, $objPage->rootId, $strLang ?: $GLOBALS['TL_LANGUAGE'])
            ->fetchAllassoc();

        // Fetch main page of page branch
        $arrL10nMainPage = $this->Database
            ->prepare('SELECT pid AS l10nPid, alias AS l10nAlias, ' . implode(',', $fields) . ' FROM tl_page_i18nl10n WHERE pid = (SELECT id FROM tl_page WHERE pid = ? AND alias = ?) AND language = ?')
            ->limit(1)
            ->execute($objPage->rootId, $objPage->mainAlias, $strLang ?: $GLOBALS['TL_LANGUAGE'])
            ->fetchAssoc();

        $arrL10nPage = $arrL10nRelatedPages[0];
        $arrL10nParentPage = $arrL10nRelatedPages[1];
        $arrL10nRootPage = $arrL10nRelatedPages[2] ?: $arrL10nRelatedPages[1]; // Use parent page as root if no root page

        // If fallback and localization are not published, return null
        if (!$objPage->i18nl10n_published && $arrL10nPage['l10nPid'] !== $objPage->id) {
            return null;
        }

        // Replace page information only if current page exists
        if ($arrL10nPage['l10nPid'] === $objPage->id) {

            // Replace current page information
            foreach ($fields as $field) {
                if ($arrL10nPage[$field]) {
                    $objPage->$field = $arrL10nPage[$field];
                } elseif ($field === 'pageTitle') { // If empty pageTitle use title
                    $objPage->$field = $arrL10nPage['title'];
                }
            }

            // Replace parent page information
            if ($arrL10nParentPage['l10nPid'] === $objPage->pid) {
                $objPage->parentAlias = $arrL10nParentPage['l10nAlias'];
                $objPage->parentTitle = $arrL10nParentPage['title'];
                $objPage->parentPageTitle = $arrL10nParentPage['pageTitle'] ?: $arrL10nParentPage['title'];
            }

            if (!empty($arrL10nMainPage)) {
                $objPage->mainAlias = $arrL10nMainPage['l10nAlias'];
                $objPage->mainTitle = $arrL10nParentPage['title'];
                $objPage->mainPageTitle = $arrL10nParentPage['pageTitle'] ?: $arrL10nParentPage['title'];
            }

            // replace root page information
            if ($arrL10nRootPage['l10nPid'] === $objPage->rootId) {
                $objPage->rootAlias = $arrL10nRootPage['l10nAlias'];
                $objPage->rootTitle = $arrL10nRootPage['title'];
                $objPage->rootPageTitle = $arrL10nRootPage['pageTitle'] ?: $arrL10nRootPage['title'];

                // Language was not replaced since this removes the options from language select
            }
        } else {
            // else at least keep current language to prevent language change and set flag
            $objPage->language            = $GLOBALS['TL_LANGUAGE'];
            $objPage->useFallbackLanguage = true;
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
    private function mapLanguagesFromDatabaseRootPageResult($objRootPage, $blnForCurrentUserOnly = false, $blnReturnFlat = false)
    {
        $arrLanguages = array();

        if ( $objRootPage->count() ) {

            if ($blnReturnFlat) {
                // Loop domains
                while ($objRootPage->next()) {
                    // Add fallback language
                    if (!$blnForCurrentUserOnly || $this->userHasLanguagePermission($objRootPage->id, $objRootPage->language)) {
                        $arrLanguages[] = $objRootPage->language;
                    }

                    // Add localizations
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
     * Get language alternatives for given tl_page id and current language
     *
     * @param \PageModel    $objPage
     *
     * @return array
     */
    public function getLanguageAlternativesByPage($objPage)
    {
        $fields = implode(',', $this->getTableFields());

        return \Database::getInstance()
            ->prepare("
                SELECT $fields
                FROM tl_page_i18nl10n
                WHERE pid = ? AND i18nl10n_published = 1 AND language != ?
                UNION
                SELECT $fields
                FROM tl_page
                WHERE id = ? AND i18nl10n_published = 1 AND language != ?
            ")
            ->execute($objPage->id, $objPage->language, $objPage->id, $objPage->language)
            ->fetchAllAssoc();
    }

    /**
     * Create domain related language array for user and group permission
     *
     * @param   Array   $arrLanguages
     *
     * @return array
     */
    private function mapLanguageOptionsForUserOrGroup(array $arrLanguages)
    {
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

    /**
     * Replace i18nl10n insert tags
     *
     * @param string $strTag
     * @param bool   $blnCache
     *
     * @return bool|string
     */
    public function replaceInsertTags($strTag, $blnCache = true)
    {
        global $objPage;

        $arrArguments = explode('::', $strTag);

        if ($arrArguments[0] === 'i18nl10n' && $arrArguments[1] === 'link') {
            $objNextPage = I18nl10n::getInstance()->findL10nWithDetails($arrArguments[2], $GLOBALS['TL_LANGUAGE']);

            if ($objNextPage === null) {
                return false;
            }

            switch ($objNextPage->type) {
                case 'redirect':

                    $strUrl = parent::replaceInsertTags($objNextPage->url);

                    if (strncasecmp($strUrl, 'mailto:', 7) === 0) {
                        $strUrl = \String::encodeEmail($strUrl);
                    }
                    break;

                case 'forward':

                    $intForwardId = $objNextPage->jumpTo ?: \PageModel::findFirstPublishedByPid($objNextPage->id)->current()->id;

                    $objNext = \PageModel::findWithDetails($intForwardId);

                    if ($objNext !== null) {
                        $strUrl = $this->generateFrontendUrl($objNext->row(), null, '');
                        break;
                    }

                // no break
                default:
                    $strUrl = $this->generateFrontendUrl($objNextPage->row(), null, '');
                    break;
            }

            $strName = $objNextPage->title;
            $strTarget = $objNextPage->target ? (($objPage->outputFormat == 'xhtml') ? LINK_NEW_WINDOW : ' target="_blank"') : '';
            $strTitle = $objNextPage->pageTitle ?: $objNextPage->title;

            return sprintf('<a href="%s" title="%s"%s>%s</a>', $strUrl, specialchars($strTitle), $strTarget, specialchars($strName));
        }

        return false;
    }
}
