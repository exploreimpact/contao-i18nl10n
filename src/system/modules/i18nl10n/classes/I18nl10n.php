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
     * @param $format
     * @param array $data
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
        $keys = array_keys($data);

        foreach ($match as &$value)
        {
            if (($key = array_search($value[1][0], $keys, TRUE)) !== FALSE || (is_numeric($value[1][0]) && ($key = array_search((int)$value[1][0], $keys, TRUE)) !== FALSE))
            {
                $len = strlen($value[1][0]);
                $format = substr_replace($format, 1 + $key, $offset + $value[1][1], $len);
                $offset -= $len - strlen(1 + $key);
            }
        }

        return vsprintf($format, $data);
    }


    /**
     * Find alias for internationalized content or use fallback language alias
     *
     * @param $arrFragments
     * @param $strLanguage
     * @return null|string
     */
    public static function findByLocalizedAliases($arrFragments, $strLanguage)
    {

        $alias = null;
        $arrAlias = array();
        $strAlias = $arrFragments[0];

        if (\Config::get('folderUrl') && $arrFragments[count($arrFragments)-2] == 'language') {
            // glue together possible aliases
            for($i = 0; count($arrFragments)-2 > $i; $i++) {
                $arrAlias[] = ($i == 0) ? $arrFragments[$i] : $arrAlias[$i-1] . '/' . $arrFragments[$i];
            }

            // reverse array to get specific entries first
            $arrAlias = array_reverse($arrAlias);

            $strAlias = implode("','", $arrAlias);
        }

        $dataBase = \Database::getInstance();

        $sql = "
            SELECT
                alias
            FROM
                tl_page
            WHERE
                (
                    id = (SELECT pid FROM tl_page_i18nl10n WHERE id = ? AND language = ?)
                    OR id = (SELECT pid FROM tl_page_i18nl10n WHERE alias IN('" . $strAlias . "') AND language = ? ORDER BY " . $dataBase->findInSet('alias', $arrAlias) . ")
                    OR alias IN('" . $strAlias . "')
                )
        ";

        if (!BE_USER_LOGGED_IN)
        {
            $time = time();
            $sql .= "
                AND (start = '' OR start < $time)
                AND (stop = '' OR stop > $time)
                AND published = 1
            ";
        }

        $sql .= "ORDER BY " . $dataBase->findInSet('alias', $arrAlias);

        $objL10n = $dataBase
            ->prepare($sql)
            ->execute(
                is_numeric($arrFragments[0]) ? $arrFragments[0] : 0,
                $strLanguage,
                $strLanguage,
                $strLanguage
            );

        if ($objL10n !== null) {
            // best match is in first item
            $arrPage = $objL10n->row();
            $alias = $arrPage['alias'];
        }

        return $alias;

    }
}