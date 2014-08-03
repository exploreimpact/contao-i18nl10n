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


    /**
     * Replace title and pageTitle with translated equivalents
     * just before display them as menu.
     *
     * @param   Array $items The menu items on the current menu level
     * @return  Array $i18n_items
     */
    public function i18nl10nNavItems(Array $items)
    {

        if(empty($items)) return false;

        //get item ids
        $item_ids = array();
        foreach($items as $row){
            $item_ids[]= $row['id'];
        }

        $time = time();
        $fields = 'alias,pid,title,pageTitle,description,language';

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
        }

        $i18n_items = array();

        foreach($items as $item)
        {

            if($GLOBALS['TL_LANGUAGE'] != $GLOBALS['TL_CONFIG']['i18nl10n_default_language'])
            {

                foreach($arrLocalizedPages as $row)
                {

                    if($row['pid'] == $item['id'])
                    {
                        $item['alias'] = $row['alias'] = $row['alias'] ? : $item['alias'];
                        $item['language'] = $row['language'];

                        if($item['type']=='forward')
                        {

                            // TODO: Refactor this
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

                                $forward_row = \Database::getInstance()
                                    ->prepare($sql)
                                    ->limit(1)
                                    ->execute($item['jumpTo'],$row['language'])
                                    ->fetchAssoc();
                            }
                            else {
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

                                $forward_row = \Database::getInstance()
                                    ->prepare($sql)
                                    ->limit(1)
                                    ->execute($item['id'],$row['language'])
                                    ->fetchAssoc();
                            }
                            $forward_row['alias'] = $item['alias'] = $forward_row['alias'] ? : $item['alias'];
                            $item['href'] = $this->generateFrontendUrl($forward_row);
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

                        // decrease iterations for each next items $items[$c]
//                        $arrLocalizedPages = array_delete($arrLocalizedPages,$d);
                        break;
                    }

                }

            }
            else {
                if($item['l10n_published'] == '') continue;
                array_push($i18n_items,$item);
            }
        }

        return $i18n_items;
    }

}

