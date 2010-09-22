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
 * Class I18nL10nModuleBreadcrumb
 *
 * @copyright  Krasimir Berov 2010 
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage
 * 
 * Just replaces mod_bredcrumb template
 */
class I18nL10nModuleBreadcrumb extends ModuleBreadcrumb
{
    /**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_i18nl10nbreadcrumb';
	protected $time;
	protected $default_lantuage;
/*
SELECT @start_id as _id, (select @start_id := pp.pid FROM tl_page pp WHERE pp.id=_id ) as pid
FROM tl_page p, (SELECT @start_id :=8) as start
where @start_id >0 
*/	
	protected function compile()
	{
        global $objPage;
        $this->time = time();
	    $this->default_lantuage = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
        $pages = array();
        $items = array();
        $pageId = $objPage->id;
        $type = null;
        //Get path to root in one go
        $objPages = $this->Database->prepare("
        SELECT @start_id as _id, 
             (select @start_id := pp.pid FROM tl_page pp WHERE pp.id=_id) as pid
        FROM tl_page p, (SELECT @start_id :=?) as start
        where @start_id >0 ")
        ->limit(20)//max levels up to root
        ->execute($pageId);
        
        $ids = array_map('I18nL10nModuleBreadcrumb::get_item_id',$objPages->fetchAllassoc());
        
        //Now get pages L10Ns
        $with_l10n = ($GLOBALS['TL_LANGUAGE']!=$this->default_lantuage);
        $language_sql = ($with_l10n?
        " AND (i.language = '".$GLOBALS['TL_LANGUAGE']."' OR i.language IS NULL) ":"");
        if($with_l10n) {
        $sql = "
SELECT p.id, p.alias, p.type, p.published, i.language, 
(CASE i.title WHEN NULL THEN p.title ELSE i.title END) as title,
(CASE i.pageTitle WHEN NULL THEN p.pageTitle ELSE i.pageTitle END) as pageTitle
FROM tl_page p
LEFT JOIN tl_page_i18nl10n i ON p.id = i.pid 
WHERE p.id IN (".implode(',',array_reverse($ids)).")
$language_sql
".(!BE_USER_LOGGED_IN ? " AND (p.start='' OR p.start<$this->time) AND (p.stop='' OR p.stop>$this->time) 
AND p.published=1" : "");
        }
        else {
        $sql = "
SELECT id, alias, title, type, pageTitle, published FROM tl_page
WHERE id IN (".implode(',',array_reverse($ids)).") "
.(!BE_USER_LOGGED_IN ? " AND (start='' OR start<$this->time) AND (stop='' OR stop>$this->time) 
AND published=1" : "")
;
        }

        $pages = $this->Database->prepare($sql)->limit(20)->execute(
                                )->fetchAllassoc();
       // Build breadcrumb menu
       $this->Template->items = $this->buildBreadcrumbMenu($pages); 
	}
    
	private function get_item_id($item){
	    return $item['_id'];
    }
    
    private function buildBreadcrumbMenu(Array $pages) {
        $with_l10n = ($GLOBALS['TL_LANGUAGE']!=$this->default_lantuage);

        $items = array();
        $root_page = array_shift($pages);
        if($this->includeRoot) {
            // Get first page
            if($with_l10n):
            $sql = "SELECT p.id, p.alias,
            (CASE i.title WHEN NULL THEN p.title ELSE i.title END) as title,
            (CASE i.pageTitle WHEN NULL THEN p.pageTitle ELSE i.pageTitle END) as pageTitle 
            FROM tl_page p
            LEFT JOIN tl_page_i18nl10n i ON p.id = i.pid  
            WHERE p.pid=? AND p.type IN ('regular','forward') " 
            . " AND (i.language = '".$GLOBALS['TL_LANGUAGE']."' OR i.language IS NULL) "
            . (!BE_USER_LOGGED_IN ? " AND (p.start='' OR p.start<$this->time) 
               AND (p.stop='' OR p.stop>$this->time) AND p.published=1" : "") . " ORDER BY p.sorting";
            else:
            $sql = "SELECT p.id, p.alias title, pageTitle 
            FROM tl_page p 
            WHERE p.pid=? AND p.type IN ('regular','forward') " 
            . (!BE_USER_LOGGED_IN ? " AND (p.start='' OR p.start<$this->time) 
               AND (p.stop='' OR p.stop>$this->time) AND p.published=1" : "") . " ORDER BY p.sorting";
            endif;
            $objFirstPage = $this->Database->prepare($sql)->limit(1)
                                           ->execute($root_page['id']);
            $first_page = $objFirstPage->fetchAssoc();
            $root_title = (strlen($first_page['pageTitle']) ? specialchars($first_page['pageTitle']) : specialchars($first_page['title']));
            $items[] = array
            (
                'isRoot' => true,
                'isActive' => false,
                'href' => (!empty($first_page) ? $this->generateFrontendUrl($first_page) : $this->Environment->base),
                'title' => $root_title,
                'link' => $root_title
            );
        }
        
        $c = count($pages)-1;
		for($i=0; $i <$c; $i++){
		    if (($pages[$i]['hide'] && !$this->showHidden) || (!$pages[$i]['published'] && !BE_USER_LOGGED_IN))
			{
				continue;
			}
                // Get href
                switch ($pages[$i]['type'])
                {
                    case 'redirect':
                        $href = $pages[$i]['url'];
    
                        if (strncasecmp($href, 'mailto:', 7) === 0)
                        {
                            $this->import('String');
                            $href = $this->String->encodeEmail($href);
                        }
                        break;
    
                    case 'forward':
                        $objNext = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
                                                  ->limit(1)
                                                  ->execute($pages[$i]['jumpTo']);
    
                        if ($objNext->numRows)
                        {
                            $href = $this->generateFrontendUrl($objNext->fetchAssoc());
                            break;
                        }
                        // DO NOT ADD A break; STATEMENT
    
                    default:
                        $href = $this->generateFrontendUrl($pages[$i]);
                        break;
                }
                $items[] = array
                (
                    'isRoot' => false,
                    'isActive' => false,
                    'href' => $href,
                    'title' => (strlen($pages[$i]['pageTitle']) ? specialchars($pages[$i]['pageTitle']) : specialchars($pages[$i]['title'])),
                    'link' => $pages[$i]['title']
                );
            }//end foreach
            $pageTitle = $pages[$c]['pageTitle'];
            $title = $pages[$c]['title'];    
            // Active article
            if (strlen($this->Input->get('articles')))
            {
                $items[] = array
                (
                    'isRoot' => false,
                    'isActive' => false,
                    'href' => $this->generateFrontendUrl($pages[$c]),
                    'title' => (strlen($pageTitle) ? specialchars($pageTitle) : specialchars($title)),
                    'link' => $title
                );
    
                list($strSection, $strArticle) = explode(':', $this->Input->get('articles'));
    
                if (is_null($strArticle))
                {
                    $strArticle = $strSection;
                }
    
                // Get article title
                $objArticle = $this->Database->prepare(
                   //TODO: Localize articles too someday
                   "SELECT title FROM tl_article WHERE id=? OR alias=?
                   AND (language=? OR language='')
                   ")
                     ->limit(1)
                     ->execute(
                       (is_numeric($strArticle) ? $strArticle : 0), 
                       $strArticle,$GLOBALS['TL_LANGUAGE']);
    
                if ($objArticle->numRows)
                {
                    $items[] = array
                    (
                        'isRoot' => false,
                        'isActive' => true,
                        'title' => specialchars($objArticle->title),
                        'link' => $objArticle->title
                    );
                }

            }
            // Active page
            else
            {
                $items[] = array
                (
                    'isRoot' => false,
                    'isActive' => true,
                    'href' => $this->generateFrontendUrl($pages[$c]),
                    'title' => (strlen($pageTitle) ? specialchars($pageTitle) : specialchars($title)),
                    'link' => $title
                );
            }
		return $items;
    }//end buildBreadcrumbMenu
}
?>