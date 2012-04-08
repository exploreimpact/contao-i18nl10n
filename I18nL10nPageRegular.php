<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  Krasimir Berov 2010-2011 
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage 
 * @license    LGPL3 
 * @filesource
 */


/**
 * Class I18nPageRegular 
 *
 * @copyright  Krasimir Berov 2010-2011
 * @author     Krasimir Berov 
 * @package    Controller
 */
class I18nL10nPageRegular extends PageRegular
{
    //override_function
    function generate(Database_Result $objPage) {
        $this->fixupCurrentLanguage();
        //get language specific page properties
        //TODO: make this configurable
        $fields = 'title,language,pageTitle,description,cssClass,'
        .'dateFormat,timeFormat,datimFormat,published,start,stop';
        $time = time();
        $l10n = $this->Database->prepare(
         "SELECT $fields from tl_page_i18nl10n WHERE pid=? AND language=? "
         . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) 
         AND published=1" : "")
         )->limit(1)->execute($objPage->id,$GLOBALS['TL_LANGUAGE']);
         //error_log( __METHOD__.':'.var_export($objPage,true) );
         if($l10n->numRows){
           $objPage->defaultPageTitle = $objPage->pageTitle;
           $objPage->defaultTitle = $objPage->title;
             foreach( explode(',',$fields) as $field ) {
                 if($l10n->$field) { 
                     $objPage->$field = $l10n->$field;
                 }
             }
         }
         return parent::generate($objPage);
    }


    /**
     * Fix up current language depending on momentary user preference.
     * Strangely $GLOBALS['TL_LANGUAGE'] is switched to the current user language if user is just
     * authentitcating and has the language property set. 
     * See system/libraries/User.php:202
     * We override this behavior and let the user temporarily use the selected by him language.
     * One workaround would be to not let the members have a language property.
     * Then this method will not be needed any more.
     */
     private function fixupCurrentLanguage(){
         $selected_language = $this->Input->post('language');
         //allow GET request for language
         if($selected_language ==false||$selected_language ==''){
            $selected_language = $this->Input->get('language');
         }
         if(
            ($selected_language !=false||$selected_language != '') && 
            in_array($selected_language,
                             deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']))
         ) {
            $_SESSION['TL_LANGUAGE'] = $GLOBALS['TL_LANGUAGE'] = $selected_language;
         }elseif(isset($_SESSION['TL_LANGUAGE'])) {
             $GLOBALS['TL_LANGUAGE'] = $_SESSION['TL_LANGUAGE'];
         }
     }
     
    /**
     * Generate an article and return it as string
     * @param integer
     * @param boolean
     * @param boolean
     * @param string
     * @return string
     * The only thing I changed here is:
     * $objArticle = new I18nL10nModuleArticle($objArticle, $strColumn);
     * TODO: Ask leo to allow something similar to 
     * $GLOBALS['FE_MOD']['navigationMenu']['navigation'] for articles 
     * (e.g. $GLOBALS['FE_MOD']['content]['article']='ModuleArticle'; )
     */
    protected function getArticle($varId, $blnMultiMode=false, $blnIsInsertTag=false, $strColumn='main')
    {
        if (!$varId)
        {
            return '';
        }

        global $objPage;
        $this->import('Database');

        // Get article
        $objRow = $this->Database->prepare("SELECT *, author AS authorId, (SELECT name FROM tl_user WHERE id=author) AS author FROM tl_article WHERE (id=? OR alias=?)" . (!$blnIsInsertTag ? " AND pid=?" : ""))
                                     ->limit(1)
                                     ->execute((is_numeric($varId) ? $varId : 0), $varId, $objPage->id);
				
		    if ($objRow->numRows < 1)
				{
					return false;
				}

				
        if (!file_exists(TL_ROOT . '/system/modules/frontend/ModuleArticle.php'))
        {
            $this->log('Class ModuleArticle does not exist', 'Controller getArticle()', TL_ERROR);
            return '';
        }
        
        // Print article as PDF
        if ($this->Input->get('pdf') == $objRow->id)
        {
            // Backwards compatibility
            if ($objRow->printable == 1)
            {
                $this->printArticleAsPdf($objRow);
            }

            // New structure
            elseif ($objRow->printable != '')
            {
                $options = deserialize($objRow->printable);

                if (is_array($options) && in_array('pdf', $options))
                {
                    $this->printArticleAsPdf($objRow);
                }
            }
        }

        $objRow->headline = $objRow->title;
        $objRow->multiMode = $blnMultiMode;
        
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['getArticle']) && is_array($GLOBALS['TL_HOOKS']['getArticle']))
        {
        	foreach ($GLOBALS['TL_HOOKS']['getArticle'] as $callback)
        	{
        		$this->import($callback[0]);
        		$this->$callback[0]->$callback[1]($objRow);
        	}
        }

        $objArticle = new I18nL10nModuleArticle($objRow, $strColumn);
        return $objArticle->generate($blnIsInsertTag);
    }
}//end class

?>
