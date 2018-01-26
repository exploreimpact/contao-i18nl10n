<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class GetSearchablePagesHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Get all i18nl10n pages and add them to the search index.
 * https://docs.contao.org/books/api/extensions/hooks/getSearchablePages.html
 */
class GetSearchablePagesHook
{
    /**
     * Add localized urls to search and sitemap indexing
     *
     * @param $arrPages
     *
     * @return array
     */
    public function getSearchablePages($arrPages)
    {
        $time           = time();
        $arrL10nPages   = array();

        $database = \Database::getInstance();

        $objPages = $database
            ->query("
              SELECT p.*, i.alias as i18nl10n_alias, i.language as i18nl10n_language, i.title as i18nl10n_title
              FROM tl_page as p
              LEFT JOIN tl_page_i18nl10n as i ON p.id = i.pid
              WHERE (p.start = '' OR p.start < $time)
                AND (p.stop = '' OR p.stop > $time)
                AND p.published = 1
                AND i.i18nl10n_published = 1
                AND p.type != 'root'
              ORDER BY p.sorting;
            ");

        while ($objPages->next()) {
            if (!$objPages->protected || \Config::get('indexProtected')) {
                $objPageWithDetails = \PageModel::findWithDetails($objPages->id);

                // Replace tl_page values with localized information
                $objPages->language         = $objPages->i18nl10n_language;
                $objPages->alias            = $objPages->i18nl10n_alias;
                $objPages->title            = $objPages->i18nl10n_title;
                $objPages->forceRowLanguage = true;

                // Create URL
                $strUrl = \Controller::generateFrontendUrl($objPages->row());
                $strUrl = ($objPageWithDetails->rootUseSSL ? 'https://' : 'http://')
                    . ($objPageWithDetails->domain ?: \Environment::get('host'))
                    . '/'
                    . $strUrl;

                // Append URL
                $arrL10nPages[] = $strUrl;
            }
        }

        return array_merge($arrPages, $arrL10nPages);
    }
}
