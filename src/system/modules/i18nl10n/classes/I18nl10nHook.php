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
 * @version     1.2.0.rc
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace Verstaerker\I18nl10n\Classes;


/**
 * Class I18nl10nHook
 *
 * Provide callbacks to modify Contao
 * behaviour related to I18N and L10N.
 *
 * @package Verstaerker\I18nl10n\Classes
 */
class I18nl10nHook extends \System
{

    /**
     * Generates url for the site according to settings from the backend
     *
     * @param array  $arrRow
     * @param string $strParams
     * @param string $strUrl
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateFrontendUrl($arrRow, $strParams, $strUrl)
    {
        if (!is_array($arrRow)) {
            throw new \Exception('not an associative array.');
        }

        $language = !empty($arrRow['robots']) || empty($arrRow['language'])
            ? $GLOBALS['TL_LANGUAGE']
            : $arrRow['language'];

        $arrLanguages = I18nl10n::getLanguagesByDomain();
        $arrL10nAlias = null;

        // try to get l10n alias by language and pid
        if ($language != $arrLanguages['default']) {
            $arrL10nAlias = \Database::getInstance()
                ->prepare('SELECT alias FROM tl_page_i18nl10n WHERE pid = ? AND language = ?')
                ->execute($arrRow['id'], $language)
                ->fetchAssoc();
        }

        $alias = is_array($arrL10nAlias)
            ? $arrL10nAlias['alias']
            : $arrRow['alias'];

        // regex to remove auto_item and language
        $regex = '@/auto_item|/language/[a-z]{2}|[\?&]language=[a-z]{2}@';

        // remove auto_item and language
        $strParams = preg_replace($regex, '', $strParams);
        $strUrl    = preg_replace($regex, '', $strUrl);

        // if alias is disabled add language to get param end return
        if (\Config::get('disableAlias')) {
            $missingValueRegex = '@(.*\?[^&]*&)([^&]*)=(?=$|&)(&.*)?@';

            if (\Config::get('useAutoItem') && preg_match($missingValueRegex, $strUrl) == 1) {
                $strUrl = preg_replace($missingValueRegex, '${1}auto_item=${2}${3}', $strUrl);
            }

            return $strUrl . '&language=' . $language;
        }

        if (\Config::get('i18nl10n_urlParam') === 'alias' && !\Config::get('disableAlias')) {

            $strL10nUrl = $alias . $strParams . '.' . $language . \Config::get('urlSuffix');

            // if rewrite is off, add environment
            if (!\Config::get('rewriteURL')) {
                $strL10nUrl = 'index.php/' . $strL10nUrl;
            }
        } elseif (\Config::get('i18nl10n_urlParam') === 'url') {

            $strL10nUrl = $language . '/' . $alias . $strParams . \Config::get('urlSuffix');

            // if rewrite is off, add environment
            if (!\Config::get('rewriteURL')) {
                $strL10nUrl = 'index.php/' . $strL10nUrl;
            }

            // if alias is missing (f.ex. index.html), add it (exclude news!)
            // search for
            // www.domain.com/
            // www.domain.com/foo/
            if (!\Config::get('disableAlias')
                && preg_match(
                       '@' . $arrRow['alias'] . '(?=\\' . \Config::get('urlSuffix') . '|/)@',
                       $strL10nUrl
                   ) === false
            ) {
                $strL10nUrl .= $alias . \Config::get('urlSuffix');
            }

        } else {
            // if get variables
            if (strpos($strUrl, '?') !== false) {
                if (strpos($strUrl, 'language=') !== false) {
                    // if variable 'language' replace it
                    $regex      = '@language=[a-z]{2}@';
                    $strL10nUrl = preg_replace(
                        $regex,
                        'language=' . $language,
                        $strUrl
                    );
                } else {
                    // if no variable 'language' add it
                    $strL10nUrl = $strUrl . '&language=' . $language;
                }
            } else {
                // if no variables define variable 'language'
                $strL10nUrl = $strUrl . '?language=' . $language;
            }
        }

        return $strL10nUrl;
    }

    /**
     * Get page id from url, based on current Contao settings
     *
     * Note: In some cases this will never be called...
     *
     * @param array $arrFragments
     *
     * @return array
     */
    public function getPageIdFromUrl(Array $arrFragments)
    {
        $arrFragments = array_map('urldecode', $arrFragments);
        $arrLanguages = I18nl10n::getLanguagesByDomain();

        // If no root pages found, return
        if (!count($arrLanguages)) {
            return $arrFragments;
        }

        // Get default language
        $strLanguage        = $arrLanguages['default'];
        $arrMappedFragments = $this->mapUrlFragments($arrFragments);

        // try to get language by i18nl10n URL
        if (\Config::get('i18nl10n_urlParam') === 'url') {
            // First entry must be language
            $strLanguage = $arrFragments[0];
        } // try to get language by suffix
        elseif (\Config::get('i18nl10n_urlParam') === 'alias' && !\Config::get('disableAlias')) {

            $intLastIndex = count($arrFragments) - 1;
            $strRegex     = '@^([_\-\pL\pN\.]*(?=\.))?\.?([a-z]{2})$@u';

            // last element should contain language info
            if (preg_match($strRegex, $arrFragments[$intLastIndex], $matches)) {
                $strLanguage = strtolower($matches[2]);
            }
        } elseif (\Input::get('language')) {
            $strLanguage = \Input::get('language');
        }

        // try to find localized page by alias
        $arrAlias = $this->findAliasByLocalizedAliases($arrMappedFragments, $strLanguage);

        // Remove first entry (will be replaced by alias further on)
        array_shift($arrMappedFragments);

        // if alias has folder, remove related entries
        if (strpos($arrAlias['alias'], '/') !== false || strpos($arrAlias['l10nAlias'], '/') !== false) {
            $arrAliasFragments = array_merge(explode('/', $arrAlias['alias']), explode('/', $arrAlias['l10nAlias']));

            // remove alias parts
            foreach ($arrAliasFragments as $strAliasFragment) {
                // if alias part is still part of arrFragments, remove it from there
                if (($key = array_search($strAliasFragment, $arrMappedFragments)) !== false) {
                    $arrMappedFragments = array_delete($arrMappedFragments, $key);
                }
            }
        }

        // Insert alias
        array_unshift($arrMappedFragments, $arrAlias['alias']);

        // Add language
        // Contao doesn't like language as part of fragments, when language is a parameter
        if (\Config::get('i18nl10n_urlParam') !== 'parameter') {
            array_push($arrMappedFragments, 'language', $strLanguage);
        }

        // Add the second fragment as auto_item if the number of fragments is even
        if (\Config::get('useAutoItem') && count($arrMappedFragments) % 2 == 0) {
            array_insert($arrMappedFragments, 1, array('auto_item'));
        }

        return $arrMappedFragments;
    }

    /**
     * Only make elements visible, that belong to this or all languages
     *
     * @param $objElement
     * @param $blnIsVisible
     *
     * @return mixed
     */
    public function isVisibleElement($objElement, $blnIsVisible)
    {
        global $objPage;

        if ($blnIsVisible && $objElement->language) {
            // check if given language is valid or fallback should be used
            $strLanguage = $objPage->useFallbackLanguage
                ? $objPage->rootLanguage
                : $GLOBALS['TL_LANGUAGE'];

            $blnIsVisible = $objElement->language === $strLanguage;
        }

        return $blnIsVisible;
    }

    /**
     * Breadcrumb callback to translate elements
     *
     * @param $arrItems  Array
     * @param $objModule \Module
     *
     * @return Array
     */
    public function generateBreadcrumb($arrItems, \Module $objModule)
    {
        $arrPages = array();

        foreach ($arrItems as $item) {
            $arrPages[] = $item['isRoot'] ? $item['data']['pid'] : $item['data']['id'];
        }

        $time = time();

        $sqlPublishedCondition = !BE_USER_LOGGED_IN
            ? " AND (start = '' OR start < $time) AND (stop = '' OR stop > $time) AND i18nl10n_published = 1 "
            : '';

        $sql = "SELECT * FROM tl_page_i18nl10n WHERE pid IN ('" . implode(',', $arrPages)
               . "') AND language = ? $sqlPublishedCondition";

        $arrL10n = \Database::getInstance()
            ->prepare($sql)
            ->execute($GLOBALS['TL_LANGUAGE'])
            ->fetchAllAssoc();

        // if translated page, replace given fields in element array
        if (count($arrL10n) > 0) {
            // each breadcrumb element
            for ($i = 0; count($arrItems) > $i; $i++) {
                // each translation
                foreach ($arrL10n as $l10n) {
                    // if translation for actual breadcrumb element
                    if ($arrItems[$i]['isRoot'] && $arrItems[$i]['data']['pid'] == $l10n['pid']
                        || !$arrItems[$i]['isRoot'] && $arrItems[$i]['data']['id'] == $l10n['pid']
                    ) {
                        if ($l10n['pageTitle']) {
                            $arrItems[$i]['title'] = $l10n['pageTitle'];
                        }
                        if ($l10n['title']) {
                            $arrItems[$i]['link'] = $l10n['title'];
                        }
                        break;
                    }
                }
            }
        }

        return $arrItems;
    }

    /**
     * loadDataContainer hook
     *
     * Add onload_callback definition when loadDataContainer hook is
     * called to define onload_callback as late as possible
     *
     * @param $strName
     */
    public function appendLanguageSelectCallback($strName)
    {
        if ($strName == 'tl_content' && \Input::get('do') == 'article') {
            $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] =
                array('tl_content_l10n', 'appendLanguageInput');
        }
    }

    /**
     * loadDataContainer hook
     *
     * Redefine button_callback for tl_content elements to allow permission
     * based display/hide.
     *
     * @param $strName
     */
    public function appendButtonCallback($strName)
    {
        // Append tl_content callbacks
        if ($strName === 'tl_content' && \Input::get('do') === 'article') {
            // Edit button
            $this->setButtonCallback(
                'tl_content',
                'edit'
            );

            // Copy button
            $this->setButtonCallback(
                'tl_content',
                'copy'
            );

            // Cut button
            $this->setButtonCallback(
                'tl_content',
                'cut'
            );

            // Delete button
            $this->setButtonCallback(
                'tl_content',
                'delete'
            );

            // Toggle button
            $this->setButtonCallback(
                'tl_content',
                'toggle'
            );
        }

        // Append tl_page callbacks
        if ($strName === 'tl_page' && \Input::get('do') === 'page') {
            // Edit button
            $this->setButtonCallback(
                'tl_page',
                'edit'
            );

            // Copy button
            $this->setButtonCallback(
                'tl_page',
                'copy'
            );

            // Copy children button
            $this->setButtonCallback(
                'tl_page',
                'copyChilds'
            );

            // Cut button
            $this->setButtonCallback(
                'tl_page',
                'cut'
            );

            // Delete button
            $this->setButtonCallback(
                'tl_page',
                'delete'
            );

            // Toggle button
            $this->setButtonCallback(
                'tl_page',
                'toggle'
            );
        }
    }

    /**
     * Set button callback for given table and operation
     *
     * @param $strTable
     * @param $strOperation
     */
    private function setButtonCallback($strTable, $strOperation)
    {
        $arrVendorCallback = $GLOBALS['TL_DCA'][$strTable]['list']['operations'][$strOperation]['button_callback'];

        switch ($strTable) {
            case 'tl_page':
                $objCallback = new \tl_page_l10n();
                break;

            case 'tl_content':
                $objCallback = new \tl_content_l10n();
                break;

            default:
                return;
        }

        // Create an anonymous function to handle callback from different DCAs
        $GLOBALS['TL_DCA'][$strTable]['list']['operations'][$strOperation]['button_callback'] =
            function () use ($strTable, $objCallback, $strOperation, $arrVendorCallback) {

                // Get callback arguments
                $arrArgs = func_get_args();

                return call_user_func_array(
                    array($objCallback, 'createButton'),
                    array($strOperation, $arrVendorCallback, $arrArgs)
                );
            };
    }

    /**
     * Find alias for internationalized content or use fallback language alias
     *
     * @param $arrFragments
     * @param $strLanguage
     *
     * @return null|array
     */
    private function findAliasByLocalizedAliases($arrFragments, $strLanguage)
    {
        $arrAlias = array
        (
            'alias'     => $arrFragments[0],
            'l10nAlias' => ''
        );
        $dataBase = \Database::getInstance();

        $arrAliasGuess = \Config::get('folderUrl')
            ? $this->createAliasGuessingArray($arrFragments)
            : array();

        $strAlias = !empty($arrAliasGuess)
            ? implode("','", $arrAliasGuess)
            : $arrFragments[0];

        // Find alias usages by language from tl_page and tl_page_i18nl10n
        $sql = "(SELECT pid as pageId, alias, 'tl_page_i18nl10n' as 'source'
                 FROM tl_page_i18nl10n
                 WHERE alias IN('" . $strAlias . "') AND language = ?)
                UNION
                (SELECT id as pageId, alias, 'tl_page' as 'source'
                 FROM tl_page
                 WHERE alias IN('" . $strAlias . "') AND language = ?)
                ORDER BY " . $dataBase->findInSet('alias', $arrAliasGuess);

        $objL10nPage = $dataBase
            ->prepare($sql)
            ->execute(
                $strLanguage,
                $strLanguage
            );

        $strHost = \Environment::get('host');

        // If page(s) where found, get l10n alias and related parent page
        while ($objL10nPage->next()) {
            $arrAlias['l10nAlias'] = $objL10nPage->alias;

            // Get tl_page with details
            $objPage = \PageModel::findWithDetails($objL10nPage->pageId);

            if ($objPage !== null) {

                // Save alias of page with related or empty domain
                if (empty($objPage->domain) || $objPage->domain === $strHost) {
                    $arrAlias['alias'] = $objPage->alias;
                    break;
                }
            }
        }

        return $arrAlias;
    }

    /**
     * Create an array of possible aliases
     *
     * @param $arrFragments
     *
     * @return array
     */
    private function createAliasGuessingArray($arrFragments)
    {
        $arrAliasGuess = array();

        if (!empty($arrFragments)) {
            // glue together possible aliases
            foreach ($arrFragments as $key => $fragment) {
                $arrAliasGuess[] = !$key
                    ? $fragment
                    : $arrAliasGuess[$key - 1] . '/' . $fragment;
            }

            // Remove everything that is not an alias
            $arrAliasGuess = array_filter(
                array_map(
                    function ($v) {
                        return preg_match('/^[\pN\pL\/\._-]+$/u', $v) ? $v : null;
                    },
                    $arrAliasGuess
                )
            );

            // Reverse array to get more specific entries first
            $arrAliasGuess = array_reverse($arrAliasGuess);
        }

        return $arrAliasGuess;

    }

    /**
     * Clean url fragments from language and auto_item
     *
     * @param $arrFragments
     *
     * @return array
     */
    private function mapUrlFragments($arrFragments)
    {
        // Delete auto_item
        if (\Config::get('useAutoItem') && $arrFragments[1] == 'auto_item') {
            $arrFragments = array_delete($arrFragments, 1);
        }

        // Delete language if first part of url
        if (\Config::get('i18nl10n_urlParam') === 'url') {
            $arrFragments = array_delete($arrFragments, 0);
        } // Delete language if part of alias
        elseif (\Config::get('i18nl10n_urlParam') === 'alias' && !\Config::get('disableAlias')) {

            $lastIndex = count($arrFragments) - 1;
            $strRegex  = '@^([_\-\pL\pN\.]*(?=\.))?\.?([a-z]{2})$@u';

            // last element should contain language info
            if (preg_match($strRegex, $arrFragments[$lastIndex], $matches)) {
                $arrFragments[$lastIndex] = $matches[1];
            }
        }

        return $arrFragments;
    }
}
