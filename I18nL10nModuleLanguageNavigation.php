<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005--2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Krasimir Berov 2010-2012 
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage 
 * @license    LGPL3 
 * @filesource
 */

/**
 * Class I18nL10nModuleLanguageNavigation - generates a languages menu.
 * The site visitor is able to swithch between languages
 * of a page. 
 *
 * @copyright  Krasimir Berov 2010-2012
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
     */
     protected function compile()
    {
        global $objPage;

        $items = array();

        $time = time();
        $arrLanguages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);
        $fields = 'alias, language, title, pageTitle';
        $sql = 'SELECT '. $fields .' FROM tl_page_i18nl10n
            WHERE pid =? AND language  IN ( \''.implode("', '",$arrLanguages).'\' )'
         .(!BE_USER_LOGGED_IN ?
        " AND (start='' OR start<$time)
        AND (stop='' OR stop>$time) 
        AND published=1" : "");

        $res_items = $this->Database->prepare($sql)
            ->execute($objPage->id)->fetchAllassoc();
        $items = array();
        
        if(!empty($res_items)) {
            $this->loadLanguageFile('languages');
            if($objPage->i18nl10n_hide == ''){
                array_unshift($res_items,array(
                   'language' => $GLOBALS['TL_CONFIG']['i18nl10n_default_language'],
                   'title' => $objPage->title,
                   'pageTitle' => $objPage->pageTitle,
                   'alias' =>$objPage->alias,)
                );
            }
            //keep the order in $arrLanguages and assign to $items 
            //only if page translation is found in database
            foreach($arrLanguages as $index =>$language) {
                foreach($res_items as $i =>$row){
                  if($row['language'] == $language){
                    array_push($items, array(
                    'alias' => $row['alias']?$row['alias']:$objPage->alias,
                    'title' => $row['title']?$row['title']:$objPage->title,
                    'pageTitle' => $row['pageTitle']?$row['pageTitle']:$objPage->pageTitle,
                    'language' => $language,
                    'isActive' => ($language == $GLOBALS['TL_LANGUAGE'])?true:false
                    ));
                    $res_item = array_delete($res_items,$i);
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
        }//end if(!empty($res_items))
        $this->Template->items = !empty($items) ? $objTemplate->parse() : '';
    }
}//end I18nL10nModuleLanguageNavigation


