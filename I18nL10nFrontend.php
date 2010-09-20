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
 * @copyright  Krasimir Berov 2010 
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage 
 * @license    LGPL3 
 * @filesource
 */


/**
 * Class I18nL10nFrontend 
 *
 * @copyright  Krasimir Berov 2010 
 * @author     Krasimir Berov 
 * @package    Controller
 * 
 * Common frontend functionalities go here
 */
class I18nL10nFrontend extends Controller
{
    /**
	 * Load database object
	 */
	protected function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}
    /**
     * Replace title and pageTitle with trnslated equivalents 
     * just before display them as menu.
     *
     * @param Array $items The menu items on the current menu level
     */	
    public function i18nl10nNavItems(Array $items){
        if(empty($items)) {
            return $items;
        }
        //get item ids
        $item_ids = array();
        foreach($items as $row){
            $item_ids[]= intval($row['id']);//just in case
        }
        $time = time();
        $fields = 'pid,title,pageTitle,description';
        $localized_pages = $this->Database->prepare('
            SELECT '. $fields .' FROM tl_page_i18nl10n
            WHERE pid IN ( '.implode(', ',$item_ids).' )
            AND language = ? '
            .(!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) 
             AND published=1" : "")
        )->limit(1000)->execute($GLOBALS['TL_LANGUAGE'])->fetchAllassoc();
        $c=0;
        foreach($items as $item){
            $d=0;
            foreach($localized_pages as $row) {
                if($row['pid']==$item['id']) {
                    $items[$c]['pageTitle'] = specialchars($row['pageTitle']);
                    $items[$c]['title'] = specialchars($row['title']);
                    $items[$c]['link'] = $row['title'];
                    $items[$c]['description'] = $row['description'];
                    array_delete($localized_pages,$d);
                    break;
                }
                $d++;
            }
        $c++;
        }
        return $items;
    }//end i18nl10nNavItems
}
?>
