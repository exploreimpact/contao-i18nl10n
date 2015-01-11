<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n modules
 * @version     1.2.0.rc
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace Verstaerker\I18nl10n\Modules;

use Verstaerker\I18nl10n\Classes\I18nl10n;


/**
 * Class ModuleI18nl10nLanguageSelection
 *
 * Generates a languages menu.
 * The site visitor is able to switch between available languages.
 *
 * @author     Patric Eberle <line-in@derverstaerker.ch>
 */
class ModuleI18nl10nLanguageSelection extends \Module
{
    /**
     * Module wrapper template
     *
     * @var string
     */
    protected $strTemplate = 'mod_i18nl10n_nav';


    /**
     * Return a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard =
                '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['i18nl10n_languageSelection'][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $strBuffer = parent::generate();

        return strlen($this->Template->items) ? $strBuffer : '';
    }


    /**
     * Generate the module
     *
     * @hooks ModuleI18nl10nLanguageSelection manipulate translation options
     */
    protected function compile()
    {
        global $objPage;

        $time       = time();
        $items      = array();
        $langNative = I18nl10n::getNativeLanguageNames();

        $sqlPublishedCondition = !BE_USER_LOGGED_IN
            ? " AND (start = '' OR start < $time) AND (stop = '' OR stop > $time) AND i18nl10n_published = 1 "
            : '';

        // Get all possible languages for this page tree
        $arrLanguages = I18nl10n::getLanguagesByRootId($objPage->rootId);

        $sql = "
            SELECT *
            FROM tl_page_i18nl10n
            WHERE
                pid = ?
                AND language IN ( '" . implode("', '", $arrLanguages['languages']) . "' )
                $sqlPublishedCondition
        ";

        $arrTranslations = \Database::getInstance()
            ->prepare($sql)
            ->execute($objPage->id)
            ->fetchAllassoc();

        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['i18nl10nLanguageSelection'])
            && is_array($GLOBALS['TL_HOOKS']['i18nl10nLanguageSelection'])
        ) {
            foreach ($GLOBALS['TL_HOOKS']['i18nl10nLanguageSelection'] as $callback) {
                $this->import($callback[0]);
                $arrTranslations = $this->$callback[0]->$callback[1]($arrTranslations);
            }
        }

        if (!empty($arrTranslations)) {
            $this->loadLanguageFile('languages');

            // Add default language
            if ($objPage->i18nl10n_published) {
                array_unshift(
                    $arrTranslations,
                    array(
                        'id'        => $objPage->id,
                        'language'  => $objPage->rootLanguage,
                        'title'     => $objPage->title,
                        'pageTitle' => $objPage->pageTitle,
                        'alias'     => $objPage->alias
                    )
                );
            }

            // keep the order in $i18nl10nLanguages and assign to $items
            // only if page translation is found in database
            foreach ($arrLanguages['languages'] as $language) {
                // check if current language has not to be shown
                if ($language === $GLOBALS['TL_LANGUAGE'] && $this->i18nl10n_langHide === 1) {
                    continue;
                }

                // loop translations
                foreach ($arrTranslations as $row) {

                    // check if language is needed
                    if ($row['language'] === $language) {
                        array_push(
                            $items,
                            array(
                                'id'        => !empty($row['pid']) ? $row['pid'] : $objPage->id,
                                'alias'     => !empty($row['alias']) ? $row['alias'] : $objPage->alias,
                                'title'     => !empty($row['title']) ? $row['title'] : $objPage->title,
                                'pageTitle' => !empty($row['pageTitle']) ? $row['pageTitle'] : $objPage->pageTitle,
                                'language'  => $language,
                                'isActive'  => ($language === $GLOBALS['TL_LANGUAGE'])
                            )
                        );
                        break;
                    }
                }
            }

            // Add classes first and last
            $last                  = (count($items) - 1);
            $items[0]['class']     = trim($items[0]['class'] . ' first');
            $items[$last]['class'] = trim($items[$last]['class'] . ' last');

            $objTemplate = new \BackendTemplate($this->i18nl10n_langTpl);

            $objTemplate->type      = get_class($this);
            $objTemplate->items     = $items;
            $objTemplate->languages = $langNative;
        }

        // add stylesheets
        if ($this->i18nl10n_langStyle != 'disable') {
            $assetsUrl = 'system/modules/i18nl10n/assets/';

            // global style
            $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang.css';

            switch ($this->i18nl10n_langStyle) {
                case 'text':
                    $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang_text.css';
                    break;
                case 'image':
                    $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang_image.css';
                    break;
                case 'iso':
                    $GLOBALS['TL_CSS'][] = $assetsUrl . 'css/i18nl10n_lang_iso.css';
                    break;
            }
        }

        $this->Template->items = !empty($items) && isset($objTemplate) ? $objTemplate->parse() : '';
    }
}
