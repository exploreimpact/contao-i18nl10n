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

namespace Verstaerker\I18nl10n\Modules;

/**
 * ModuleI18nl10nLanguageNavigation
 *
 * Generates a languages menu.
 * The site visitor is able to switch between available languages.
 *
 * @copyright  Verstärker, Patric Eberle 2014; Krasimir Berov 2010-2013
 * @author     Patric Eberle <line-in@derverstaerker.ch>; Krasimir Berov
 * @package    MultiLanguagePage
 */
class ModuleI18nl10nLanguageNavigation extends \Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_i18nl10n_nav';


    /**
     * Return a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['i18nl10nLanguageNavigation'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $strBuffer = parent::generate();

        return strlen($this->Template->items) ? $strBuffer : '';
    }


    /**
     * Generate the module
     *
     * @hooks ModuleI18nl10nLanguageNavigation manipulate translation options
     */
     protected function compile()
    {
        global $objPage;

        $time = time();
        $arrLanguages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);
        $sql = "
            SELECT
                *
            FROM
                tl_page_i18nl10n
            WHERE
                pid = ?
                AND language IN ( '" . implode("', '",$arrLanguages) . "' )
        ";

        if(!BE_USER_LOGGED_IN) {
            $sql .= "
                AND (start = '' OR start < $time)
                AND (stop = '' OR stop > $time)
                AND published = 1
            ";
        }

        $arrTranslations = \Database::getInstance()
            ->prepare($sql)
            ->execute($objPage->id)
            ->fetchAllassoc();

        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['i18nl10nLanguageNavigation'])
            && is_array($GLOBALS['TL_HOOKS']['i18nl10nLanguageNavigation']))
        {
            foreach ($GLOBALS['TL_HOOKS']['i18nl10nLanguageNavigation'] as $callback)
            {
                $this->import($callback[0]);
                $arrTranslations = $this->$callback[0]->$callback[1]($arrTranslations);
            }
        }

        $items = array();

        if(!empty($arrTranslations)) {
            $this->loadLanguageFile('languages');

            if($objPage->l10n_published != ''){
                array_unshift($arrTranslations, array(
                   'id' => $objPage->id,
                   'language' => $GLOBALS['TL_CONFIG']['i18nl10n_default_language'],
                   'title' => $objPage->title,
                   'pageTitle' => $objPage->pageTitle,
                   'alias' => $objPage->alias
                ));
            }

            // keep the order in $arrLanguages and assign to $items
            // only if page translation is found in database
            foreach($arrLanguages as $language) {

                // check if current language has not to be shown
                if($language == $GLOBALS['TL_LANGUAGE'] && $this->i18nl10nLangHide == 1) continue;

                // loop translations
                foreach($arrTranslations as $row){

                    // check if language is needed
                    if($row['language'] == $language){
                        array_push($items, array(
                            'id' => $row['pid']?$row['pid']:$objPage->id,
                            'alias' => $row['alias'] ?: $objPage->alias,
                            'title' => $row['title'] ?: $objPage->title,
                            'pageTitle' => $row['pageTitle']?: $objPage->pageTitle,
                            'language' => $language,
                            'isActive' => ($language == $GLOBALS['TL_LANGUAGE']) ? true : false
                        ));
                        break;
                    }
                }
            }

            // Add classes first and last
            $last = (count($items) - 1);
            $items[0]['class'] = trim($items[0]['class'] . ' first');
            $items[$last]['class'] = trim($items[$last]['class'] . ' last');

            $objTemplate = new \BackendTemplate($this->i18nl10nLangTpl);
            $objTemplate->type = get_class($this);
            $objTemplate->items = $items;
            $objTemplate->languages = $GLOBALS['TL_LANG']['LNG'];
        }

        // add stylesheets
        if($this->i18nl10nLangStyle != 'disable') {
            $assetsUrl = 'system/modules/core_i18nl10n/assets/';

            // global style
            $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang.css';

            switch($this->i18nl10nLangStyle) {
                case 'text':
                    $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang_text.css';
                    break;
                case 'image':
                    $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang_image.css';
                    break;
            }
        }

        $this->Template->items = !empty($items) ? $objTemplate->parse() : '';
    }
}