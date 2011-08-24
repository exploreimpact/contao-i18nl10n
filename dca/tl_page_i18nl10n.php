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

$this->loadLanguageFile('languages');

/**
 * Load class tl_page and its labels
 */
$this->loadLanguageFile('tl_page');
$this->loadDataContainer('tl_page');

$_tl_page_i18nl10n = &$GLOBALS['TL_LANG']['tl_page_i18nl10n'];
/**
 * Table tl_page_i18nl10n 
 */
$GLOBALS['TL_DCA']['tl_page_i18nl10n'] = array
(
    
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'ptable'                      => 'tl_page',
        'onload_callback' => array
        (
            array('tl_page_i18nl10n','localize_all'),
            array('tl_page', 'addBreadcrumb'),
        ),
    
    ),

    // List
    'list' => array
    (
        'sorting' => array
    (
        'mode'                    => 6,
        'fields'                  => array('language DESC' ),
        'panelLayout'             => 'filter'
    ),
    'label' => array
    (
        'fields' => array('title', 'language','language'),
        //'format' => '%s <span style="color:#b3b3b3; padding-left:3px;'
        //        . '"><img style="vertical-align:middle"'
        //        .' src="system/modules/i18nl10n/html/flag_icons/png/%s.png" /> [%s]</span>',
        'label_callback' => array('tl_page_i18nl10n','addIcon')
    ),
    'global_operations' => array
    (
    'localize_all' => array
    (
        'label'               => &$_tl_page_i18nl10n['localize_all'],
        'href'                => 'localize_all=1',
        'class'               => 'header_localize_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
    ),
    'toggleNodes' => array
    (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['toggleNodes'],
        'href'                => '&amp;ptg=all',
        'class'               => 'header_toggle'
    ),
    'all' => array
    (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
    ),

    
    ),
    'operations' => array
    (
        'edit' => array
        (
            'label'               => &$_tl_page_i18nl10n['edit'],
            'href'                => 'act=edit',
            'icon'                => 'edit.gif'
        ),
        'copy' => array
        (
            'label'               => &$_tl_page_i18nl10n['copy'],
            'href'                => 'act=copy',
            'icon'                => 'copy.gif'
        ),
        'delete' => array
        (
            'label'               => &$_tl_page_i18nl10n['delete'],
            'href'                => 'act=delete',
            'icon'                => 'delete.gif',
            'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
        ),
        'show' => array
        (
            'label'               => &$_tl_page_i18nl10n['show'],
            'href'                => 'act=show',
            'icon'                => 'show.gif'
        )
    )
    ),
    
/**
	// Subpalettes
	'subpalettes' => array
	(
		''                            => ''
	),
 */

);
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['palettes'] = array
    (
        'default' => '{menu_legend},title,' 
     //TODO Implement commented fields
     //alias,
     .'language;'
            .'{meta_legend},pageTitle,description;'
            .'{time_legend:hide},dateFormat,timeFormat,datimFormat;'
            .'{expert_legend:hide},cssClass;{publish_legend},published'
            .',start,stop'
            ,
    );
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields'] = array
	(
	     'title'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['title'],
	     //TODO: add alias localized support so
	     //for example alias 'начало' links to 'home' with l10n enabled
	     // may be use $GLOBALS['TL_HOOKS']['getPageIdFromUrl']
	     //'alias'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['alias'],
         'pageTitle'   => &$GLOBALS['TL_DCA']['tl_page']['fields']['pageTitle'],
         'description' => &$GLOBALS['TL_DCA']['tl_page']['fields']['description'],
         'cssClass'    => &$GLOBALS['TL_DCA']['tl_page']['fields']['cssClass'],
         'dateFormat'  => &$GLOBALS['TL_DCA']['tl_page']['fields']['dateFormat'],
         'timeFormat'  => &$GLOBALS['TL_DCA']['tl_page']['fields']['timeFormat'],
         'datimFormat' => &$GLOBALS['TL_DCA']['tl_page']['fields']['datimFormat'],
         'sitemapName' => &$GLOBALS['TL_DCA']['tl_page']['fields']['sitemapName'],
         'published'   => &$GLOBALS['TL_DCA']['tl_page']['fields']['published'],
         'start'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['start'],
         'stop'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['stop']
         );

$i18nl10n_languages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);
$i18nl10n_default_language = &$GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
foreach($i18nl10n_languages as $k=>$v){
    if($v==$i18nl10n_default_language) {
        $i18nl10n_languages = array_delete($i18nl10n_languages,$k);
        break;
    }
}
$GLOBALS['i18nl10n_languages'] = $i18nl10n_languages;

$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['language'] = array_merge(
        $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
        array(
            'label'     => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
             'filter'   => true,
            'inputType' => 'select',
            'options'   => $i18nl10n_languages,
            'reference'  => &$GLOBALS['TL_LANG']['LNG'])
);

/**
 * Class tl_page_i18nl10n
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2010
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_page_i18nl10n extends Backend
{
/**
	 * Generate a localization icon
	 * @param array
	 * @param string
	 * @return string
	 */
	public function addIcon($row, $label, DataContainer $dc=null, $imageAttribute='')
	{
	    //$image = $this->generateImage('iconPLAIN.gif', '', $folderAttribute);
	    $label ='<span style="color:#b3b3b3; padding-left:3px;'
                . '"><img style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/flag_icons/png/'
                .$row['language'].'.png" /> '.specialchars($row['title']).' ['.$GLOBALS['TL_LANG']['LNG'][$row['language']].']</span>';
	    return $label;
	}
	/**
	 * Localize all pages with a twist.
	 */
	public function localize_all() {
	    if($this->Input->get('localize_all')
	       && !$this->Input->post('localize_all')
	       ) {
            $flag = '<img style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/flag_icons/png/'
                .$GLOBALS['TL_CONFIG']['i18nl10n_default_language']
                .'.png" /> ';
            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
           
            '<form method="post" action="contao/main.php?do=i18nl10n"
            ><div id="i18nl10n_localise_all_confirm">'
            .sprintf($GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all_q'],
                    $flag.$GLOBALS['TL_LANG']['LNG'][$GLOBALS['TL_CONFIG']['i18nl10n_default_language']]
                    )
            .'<div class="tl_submit_container"><input 
            type="submit" value="'
            .utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['yes']).'" 
            class="tl_submit" name="localize_all_" /> <a
            href="contao/main.php?do=i18nl10n">'
            .utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['no']).'</a>&nbsp;
            </div></div><input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'"></form>'
            ;
        }
        //localise all pages 
        elseif($this->Input->post('localize_all_')){

                 
             foreach($GLOBALS['i18nl10n_languages'] as $l) {
            $SQL="
            INSERT INTO tl_page_i18nl10n (
                 pid,sorting,tstamp,language,title,type,
                 pageTitle,description,cssClass,
                 published,start,stop,dateFormat,timeFormat,datimFormat)
            SELECT p.id AS pid, p.sorting, p.tstamp, '$l' AS language, 
                 p.title, p.type, p.pageTitle, p.description, p.cssClass, 
                 p.published, p.start, p.stop, p.dateFormat, p.timeFormat, p.datimFormat
                 FROM tl_page p LEFT JOIN tl_page_i18nl10n i 
                 ON p.id = i.pid AND i.language='$l' 
                 WHERE p.language='"
                 .$GLOBALS['TL_CONFIG']['i18nl10n_default_language']
                 ."' and p.type !='root' AND i.pid IS NULL";
                 //echo $SQL;
                 $this->Database->prepare($SQL)->execute();
             }
        }
    }
	
}//end class tl_page_i18nl10n
