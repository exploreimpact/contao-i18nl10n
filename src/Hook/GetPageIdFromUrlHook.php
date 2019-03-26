<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Verstaerker\I18nl10nBundle\Classes\I18nl10n;

/**
 * Class GetPageIdFromUrlHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Translate the i18nl10n URL fragments to their corresponding parent counterpart with language parameter.
 * https://docs.contao.org/books/api/extensions/hooks/getPageIdFromUrl.html
 */
class GetPageIdFromUrlHook
{
    /**
     * Get page id from url, based on current Contao settings
     *
     * Note: In some cases this will never be called...
     *
     * @param array $arrFragments
     *
     * @return array
     */
    public function getPageIdFromUrl(array $arrFragments)
    {
        // Check if url fragments are available (see #66)
        if (empty($arrFragments[0])) {
            return $arrFragments;
        }

        $arrFragments = array_map('urldecode', $arrFragments);
        $arrLanguages = I18nl10n::getInstance()->getLanguagesByDomain();

        // If no root pages found, return
        if (!count($arrLanguages)) {
            return $arrFragments;
        }


        /*
         * Extract the language snippet from URL
         */
        // Get default language
        $strLanguage        = $arrLanguages['default'];
        $arrMappedFragments = $this->mapUrlFragments($arrFragments);

        // try to get language by i18nl10n URL
        if (\Config::get('i18nl10n_urlParam') === 'url') {
            // First entry must be language
            $strLanguage = $arrFragments[0];
        // try to get language by suffix
        } elseif (\Config::get('i18nl10n_urlParam') === 'alias' && !\Config::get('disableAlias')) {
            $intLastIndex = count($arrFragments) - 1;
            $strRegex     = '@^([_\-\pL\pN\.]*(?=\.))?\.?([a-z]{2})$@u';

            // last element should contain language info
            if (preg_match($strRegex, $arrFragments[$intLastIndex], $matches)) {
                $strLanguage = strtolower($matches[2]);
            }
        } elseif (\Input::get('language')) {
            $strLanguage = \Input::get('language');
        }


        /*
         * Arrange correct i18nl10n alias
         */
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


        /*
         * Add language to URL fragments
         */
        // Add language
        // Contao doesn't like language as part of fragments, when language is a parameter
        if (\Config::get('i18nl10n_urlParam') !== 'parameter') {
            array_push($arrMappedFragments, 'language', $strLanguage);
        }

        // Add the second fragment as auto_item if the number of fragments is even
        if (\Config::get('useAutoItem') && count($arrMappedFragments) % 2 == 0) {
            array_insert($arrMappedFragments, 1, array('auto_item'));
        }

        // Restore the urlSuffix config if it has been removed in initializeSystem hook
        if (\Config::has('tmpUrlSuffix')) {
            \Config::set('urlSuffix', \Config::get('tmpUrlSuffix'));
            \Config::set('tmpUrlSuffix', '');
        }

        // Consider that - as Contao native does - if we don't have a fragment, make it / to find homepage.
        if ($arrMappedFragments[0] === "") {
            $arrMappedFragments[0] = "/";
        }

        return $arrMappedFragments;
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
        if (\Config::get('useAutoItem') && $arrFragments[1] === 'auto_item') {
            $arrFragments = array_delete($arrFragments, 1);
        }

        // Delete language if first part of url
        if (\Config::get('i18nl10n_urlParam') === 'url') {
            $arrFragments = array_delete($arrFragments, 0);
        // Delete language if part of alias
        } elseif (\Config::get('i18nl10n_urlParam') === 'alias' && !\Config::get('disableAlias')) {
            $lastIndex = count($arrFragments) - 1;
            $strRegex  = '@^([_\-\pL\pN\.]*(?=\.))?\.?([a-z]{2})$@u';

            // last element should contain language info
            if (preg_match($strRegex, $arrFragments[$lastIndex], $matches)) {
                $arrFragments[$lastIndex] = $matches[1];
            }
        }

        return $arrFragments;
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
        $arrAlias = array(
            'alias'     => $arrFragments[0],
            'l10nAlias' => ''
        );

        $arrAliasGuess = \Config::get('folderUrl')
            ? $this->createAliasGuessingArray($arrFragments)
            : array();

        $strAlias = !empty($arrAliasGuess)
            ? implode("','", $arrAliasGuess)
            : $arrFragments[0];

        $database = \Database::getInstance();

        // Find alias usages by language from tl_page and tl_page_i18nl10n
        $sql = "(SELECT pid as pageId, alias, 'tl_page_i18nl10n' as 'source'
                 FROM tl_page_i18nl10n
                 WHERE alias IN(?) AND language = ?)
                UNION
                (SELECT id as pageId, alias, 'tl_page' as 'source'
                 FROM tl_page
                 WHERE alias IN(?))
                ORDER BY "
            . $database->findInSet('alias', $arrAliasGuess) . ", "
            . $database->findInSet('source', array('tl_page_i18nl10n', 'tl_page'));

        $objL10nPage = $database
            ->prepare($sql)
            ->execute(
                $strAlias,
                $strLanguage,
                $strAlias
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
}
