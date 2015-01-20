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
 * @version     1.2.0
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace Verstaerker\I18nl10n\Classes;

/**
 * Class I18nl10nFrontend
 * Common frontend functions go here
 *
 * @package    Controller
 */
class I18nl10nFrontend extends \Controller
{
    /**
     * Load database object
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Replace title and pageTitle with translated equivalents
     * just before display them as menu. Also set visible elements.
     *
     * @param array $items
     * @return Array
     */
    public function l10nNavItems(Array $items)
    {
        return self::i18nl10nNavItems($items, true);
    }

    /**
     * Replace title and pageTitle with translated equivalents
     * just before display them as menu.
     *
     * @param   Array $items The menu items on the current menu level
     * @param   Bool $blnUseFallback Keep original item if no translation found
     * @return  Array $i18n_items
     */
    public function i18nl10nNavItems(Array $items, $blnUseFallback = false)
    {
        if (empty($items)) {
            return false;
        }

        /**
         * Info:
         * Be aware that Contao 3.4.0 supports 'isActive' only for start pages
         * with the alias 'index'. See ticket #7562 (https://github.com/contao/core/issues/7562)
         */

        $arrLanguages = I18nl10n::getLanguagesByDomain();

        //get item ids
        $item_ids = array();

        foreach ($items as $row)
        {
            $item_ids[] = $row['id'];
        }

        $arrI18nItems = array();

        if ($GLOBALS['TL_LANGUAGE'] != $arrLanguages['default'])
        {
            $time = time();
            $fields = 'alias,pid,title,pageTitle,description,url,language';
            $sqlPublishedCondition = !$blnUseFallback && !BE_USER_LOGGED_IN
                ? " AND (start='' OR start < $time) AND (stop='' OR stop > $time) AND i18nl10n_published = 1 "
                : '';

            $sql = "
                SELECT $fields
                FROM tl_page_i18nl10n
                WHERE
                    pid IN (" . implode(', ', $item_ids) . ")
                    AND language = ?
                    $sqlPublishedCondition";

            $arrLocalizedPages = \Database::getInstance()
                ->prepare($sql)
                ->limit(1000)
                ->execute($GLOBALS['TL_LANGUAGE'])
                ->fetchAllassoc();

            foreach ($items as $item)
            {

                $foundItem = false;

                foreach ($arrLocalizedPages as $row)
                {

                    if ($row['pid'] == $item['id'])
                    {

                        $foundItem = true;
                        $alias = $row['alias'] ? : $item['alias'];

                        $item['alias'] = $alias;
                        $row['alias'] = $alias;
                        $item['language'] = $row['language'];

                        switch ($item['type'])
                        {
                            case 'forward':
                                $forwardRow = self::getI18nForward($item, $row['language']);
                                $forwardRow['alias'] = $item['alias'] = $forwardRow['alias'] ? : $item['alias'];
                                $item['href'] = $this->generateFrontendUrl($forwardRow);
                                break;

                            case 'redirect';
                                if($row['url'])
                                {
                                    $item['href'] = $row['url'];
                                }
                                break;

                            default:
                                $item['href'] = $this->generateFrontendUrl($item);
                                break;
                        }

                        $item['pageTitle'] = specialchars($row['pageTitle'], true);
                        $item['title'] = specialchars($row['title'], true);
                        $item['link'] = $item['title'];
                        $item['description'] = str_replace(
                            array('\n', '\r'),
                            array(' ', ''),
                            specialchars($row['description'])
                        );

                        array_push($arrI18nItems, $item);

                    }

                }

                if ($blnUseFallback && !$foundItem)
                {
                    array_push($arrI18nItems, $item);
                }

            }
        }
        else
        {
            foreach ($items as $item)
            {
                if (!$blnUseFallback && $item['i18nl10n_published'] == '') continue;
                array_push($arrI18nItems, $item);
            }
        }

        return $arrI18nItems;
    }

    /**
     * Get forward items
     *
     * @param array $item
     * @param string $lang
     * @return array|false
     */
    private function getI18nForward(Array $item, $lang)
    {
        if ($item['jumpTo'])
        {
            // If jumpTo is set, get the target page
            $sql = '
              SELECT *
              FROM tl_page_i18nl10n
              WHERE
                pid = ?
                AND language = ?
            ';

            $request = \Database::getInstance()
                ->prepare($sql)
                ->limit(1)
                ->execute($item['jumpTo'], $lang);

            $i18nl = $request->fetchAssoc();
        }
        else
        {
            // If jumpTo is not set, get first published subpage
            $i18nl = I18nl10n::findFirstPublishedL10nRegularPageByPid($item['id'], $lang);
        }

        return $i18nl;
    }
}
