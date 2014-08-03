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


    public function l10nNavItems(Array $items) {
        return self::i18nl10nNavItems($items, true);
    }


    /**
     * Replace title and pageTitle with translated equivalents
     * just before display them as menu.
     *
     * @param   Array $items The menu items on the current menu level
     * @param   Bool $useFallback Keep original item if no translation found
     * @return  Array $i18n_items
     */
    public function i18nl10nNavItems(Array $items, $useFallback = false)
    {

        if(empty($items)) return false;

        //get item ids
        $item_ids = array();
        foreach($items as $row){
            $item_ids[]= $row['id'];
        }

        $time = time();
        $fields = 'alias,pid,title,pageTitle,description,language';
        $i18n_items = array();

        if($GLOBALS['TL_LANGUAGE'] != $GLOBALS['TL_CONFIG']['i18nl10n_default_language']){
            $sql = "
                SELECT
                    $fields
                FROM
                    tl_page_i18nl10n
                WHERE
                    pid IN (" . implode(', ',$item_ids) . ")
                AND language = ?
            ";

            if(!BE_USER_LOGGED_IN) {
                $sql .= "
                    AND (start='' OR start < $time)
                    AND (stop='' OR stop > $time)
                    AND l10n_published = 1
                ";
            }

            $arrLocalizedPages = \Database::getInstance()
                ->prepare($sql)
                ->limit(1000)
                ->execute($GLOBALS['TL_LANGUAGE'])
                ->fetchAllassoc();

            foreach($items as $item)
            {

                $foundItem = false;

                foreach($arrLocalizedPages as $row)
                {

                    if($row['pid'] == $item['id'])
                    {

                        $foundItem = true;

                        $item['alias'] = $row['alias'] = $row['alias'] ? : $item['alias'];
                        $item['language'] = $row['language'];

                        if($item['type'] == 'forward')
                        {
                            $forwardRow = self::getI18nForward($item, $row['language']);
                            $forwardRow['alias'] = $item['alias'] = $forwardRow['alias'] ? : $item['alias'];
                            $item['href'] = $this->generateFrontendUrl($forwardRow);
                        }
                        else
                        {
                            $item['href'] = $this->generateFrontendUrl($item);
                        }

                        $item['pageTitle'] = specialchars($row['pageTitle'], true);
                        $item['title'] = specialchars($row['title'], true);
                        $item['link'] = $item['title'];
                        $item['description'] = str_replace(
                            array("\n", "\r"),
                            array(' ' , ''),
                            specialchars($row['description'])
                        );

                        array_push($i18n_items,$item);

                    }

                }

                if($useFallback && !$foundItem) {
                    array_push($i18n_items, $item);
                }

            }
        }
        else
        {
            foreach($items as $item)
            {
                if($item['l10n_published'] == '') continue;
                array_push($i18n_items,$item);
            }
        }

        return $i18n_items;
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
        if($item['jumpTo'])
        {
            $sql = "
              SELECT
                *
              FROM
                tl_page_i18nl10n
              WHERE
                pid = ?
                AND language = ?
            ";

            $request = \Database::getInstance()
                ->prepare($sql)
                ->limit(1)
                ->execute($item['jumpTo'],$lang);

            $i18nl = $request->fetchAssoc();
        }
        else
        {
            $time = time();
            $sql = "
                SELECT
                  *
                FROM
                  tl_page_i18nl10n
                WHERE
                  pid = (
                    SELECT
                      id
                    FROM
                      tl_page
                    WHERE
                      pid = ?
                      AND type = 'regular'";

            if(!BE_USER_LOGGED_IN) {
                $sql .= "
                    AND (start='' OR start<$time)
                    AND (stop='' OR stop>$time)
                    AND published=1";
            }

            $sql .= "
                    ORDER BY
                        sorting
                    LIMIT 0,1
                )
                AND
                    language = ?";

            $request = \Database::getInstance()
                ->prepare($sql)
                ->limit(1)
                ->execute($item['id'],$lang);

            $i18nl = $request->fetchAssoc();
        }

        return $i18nl;
    }

}

