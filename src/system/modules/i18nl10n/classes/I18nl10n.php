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
    public static function vnsprintf( $format, array $data)
    {
        preg_match_all(
            '/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x',
            $format,
            $match,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        $offset = 0;
        $keys = array_keys($data);

        foreach( $match as &$value )
        {
            if ( ( $key = array_search( $value[1][0], $keys, TRUE) ) !== FALSE || ( is_numeric( $value[1][0] ) && ( $key = array_search( (int)$value[1][0], $keys, TRUE) ) !== FALSE) )
            {
                $len = strlen( $value[1][0]);
                $format = substr_replace( $format, 1 + $key, $offset + $value[1][1], $len);
                $offset -= $len - strlen( 1 + $key);
            }
        }

        return vsprintf( $format, $data);
    }


    /**
     * Create localization for all pages
     *
     * @returns void
     */
    public static function localizeAll()
    {

        $table = 'tl_page';

        if(\Input::get('localize_all') && !\Input::post('localize_all')) {
            $flag = '<img class="i18nl10n_flag"'
                . ' src="system/modules/i18nl10n/assets/img/flag_icons/'
                . $GLOBALS['TL_CONFIG']['i18nl10n_default_language']
                . '.png" />&nbsp;';

            $message = sprintf($GLOBALS['TL_LANG']['tl_page']['msg_localize_all'], $flag . $GLOBALS['TL_LANG']['LNG'][$GLOBALS['TL_CONFIG']['i18nl10n_default_language']]);

            $newLanguages = '<ul class="i18nl10n_page_language_listing">';

            foreach(deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']) as $language) {
                if($language != $GLOBALS['TL_CONFIG']['i18nl10n_default_language']) {
                    $newLanguages .=
                        '<li><img class="i18nl10n_flag" src="system/modules/i18nl10n/assets/img/flag_icons/'
                        . $language
                        . '.png" /> '
                        . $GLOBALS['TL_LANG']['LNG'][$language]
                        . '</li>';
                }
            }

            $newLanguages .= '</ul>';

            $GLOBALS['TL_DCA'][$table]['list']['sorting']['breadcrumb'] .=
                '<form method="post" action="contao/main.php?do=' . \Input::get('do') . '">'
                . '<div class="i18nl10n_page_message">' . $message . $newLanguages
                . '<div class="tl_submit_container">'
                . '<a href="contao/main.php?do=' . \Input::get('do') . '">'
                . utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['no'])
                . '</a>'
                . '<input type="submit" value="' . utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['yes']) . '" class="tl_submit" name="localize_all_" />'
                . '</div></div>'
                . '<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'"></form>';
        }
        //localise all pages
        elseif(\Input::post('localize_all_')) {

            $defaultLanguage = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
            $i18nl10n_languages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);

            foreach($i18nl10n_languages as $lang) {

                if($defaultLanguage == $lang) continue;

                $sql = "
                  INSERT INTO
                    tl_page_i18nl10n
                    (
                      pid,
                      sorting,
                      tstamp,
                      language,
                      title,
                      type,
                      pageTitle,
                      description,
                      cssClass,
                      alias,
                      l10n_published,
                      start,
                      stop,
                      dateFormat,
                      timeFormat,
                      datimFormat
                    )
                  SELECT
                    p.id AS pid,
                    p.sorting,
                    p.tstamp,
                    '$lang' AS language,
                    p.title,
                    p.type,
                    p.pageTitle,
                    p.description,
                    p.cssClass,
                    p.alias,
                    p.published,
                    p.start,
                    p.stop,
                    p.dateFormat,
                    p.timeFormat,
                    p.datimFormat
                  FROM
                    tl_page p
                  LEFT JOIN
                    tl_page_i18nl10n i
                      ON p.id = i.pid
                      AND i.language='$lang'
                  WHERE
                    (
                      p.language='$defaultLanguage'
                      OR p.language=''
                    )
                    AND p.type !='root'
                    AND i.pid IS NULL
                ";

                \Database::getInstance()
                    ->prepare($sql)
                    ->execute();
            }
        }
    }
}