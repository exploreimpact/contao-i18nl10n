<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class GenerateBreadcrumbHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * https://docs.contao.org/books/api/extensions/hooks/generateBreadcrumb.html
 */
class GenerateBreadcrumbHook
{
    /**
     * Breadcrumb callback to translate elements
     *
     * @param $arrItems  array
     * @param $objModule \Module
     *
     * @return array
     */
    public function generateBreadcrumb($arrItems, \Module $objModule)
    {
        $arrPages = array();

        foreach ($arrItems as $item) {
            $arrPages[] = $item['isRoot'] ? $item['data']['pid'] : $item['data']['id'];
        }

        $time = time();
        $database = \Database::getInstance();

        $sqlPublishedCondition = BE_USER_LOGGED_IN
            ? ''
            : " AND (start = '' OR start < $time) AND (stop = '' OR stop > $time) AND i18nl10n_published = 1 ";

        $sql = 'SELECT * FROM tl_page_i18nl10n WHERE '
            . $database->findInSet('pid', $arrPages)
            . ' AND language = ? '
            . $sqlPublishedCondition;

        $arrL10n = $database
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
}
