<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


/**
 * Class I18nL10nModuleLanguageNavigation - generates a languages menu.
 * The site visitor is able to swithch between languages
 * of a page. 
 *
 * @copyright  Krasimir Berov 2010-2013
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage
 */
class I18nL10nModuleLanguageNavigation extends Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_i18nl10nnav';
    /**
     * Return a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /* TODO: find out what can be done here*/
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### LANGUAGE NAVIGATION ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
            
        }
        $strBuffer = parent::generate();
        return strlen($this->Template->items) ? $strBuffer : '';

    }

    /**
     * Generate the module
     *
     * @hooks i18nl10nLanguageNavigation manipulate translation options
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
                pid =?
                AND language IN ( '" . implode("', '",$arrLanguages) . "' )
        ";

        if(!BE_USER_LOGGED_IN) {
            $sql .= "
                AND (start='' OR start<$time)
                AND (stop='' OR stop>$time)
                AND published=1
            ";
        }

        $arrTranslations = $this->Database->prepare($sql)
            ->execute($objPage->id)->fetchAllassoc();

        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['i18nl10nLanguageNavigation']) && is_array($GLOBALS['TL_HOOKS']['i18nl10nLanguageNavigation']))
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
            if($objPage->i18nl10n_hide == ''){
                array_unshift($arrTranslations,array(
                   'id' => $objPage->id,
                   'language' => $GLOBALS['TL_CONFIG']['i18nl10n_default_language'],
                   'title' => $objPage->title,
                   'pageTitle' => $objPage->pageTitle,
                   'alias' =>$objPage->alias,)
                );
            }
            //keep the order in $arrLanguages and assign to $items 
            //only if page translation is found in database
            foreach($arrLanguages as $index => $language) {
                foreach($arrTranslations as $i =>$row){
                  if($row['language'] == $language){
                    array_push($items, array(
                    'id' => $row['pid']?$row['pid']:$objPage->id,
                    'alias' => $row['alias']?$row['alias']:$objPage->alias,
                    'title' => $row['title']?$row['title']:$objPage->title,
                    'pageTitle' => $row['pageTitle']?$row['pageTitle']:$objPage->pageTitle,
                    'language' => $language,
                    'isActive' => ($language == $GLOBALS['TL_LANGUAGE'])?true:false
                    ));
                    $res_item = array_delete($arrTranslations,$i);
                    break;
                  }
                }
            }
            // Add classes first and last
            $items[0]['class'] = trim($items[0]['class'] . ' first');
            $last = (count($items) - 1);
            $items[$last]['class'] = trim($items[$last]['class'] . ' last');
            $objTemplate = new BackendTemplate($this->navigationTpl);

            $objTemplate->type = get_class($this);
            $objTemplate->items = $items;
            $objTemplate->languages = $GLOBALS['TL_LANG']['LNG'];
        }

        $this->Template->items = !empty($items) ? $objTemplate->parse() : '';
    }
}//end I18nL10nModuleLanguageNavigation


