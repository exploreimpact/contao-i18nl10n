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
         //TODO: implement tl_page_i18nl10n::checkPermission
            //array('tl_page_i18nl10n', 'checkPermission'),
            array('tl_page', 'addBreadcrumb'),
            //array('tl_page', 'setDefaultLanguage')
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
    )
    ),
    'operations' => array
    (
        'edit' => array
        (
            'label'               => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit'],
            'href'                => 'act=edit',
            'icon'                => 'edit.gif'
        ),
        'copy' => array
        (
            'label'               => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy'],
            'href'                => 'act=copy',
            'icon'                => 'copy.gif'
        ),
        'delete' => array
        (
            'label'               => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'],
            'href'                => 'act=delete',
            'icon'                => 'delete.gif',
            'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
        ),
        'show' => array
        (
            'label'               => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show'],
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
            //.'{time_legend:hide},dateFormat,timeFormat,datimFormat;'
            .'{expert_legend:hide},cssClass;{publish_legend},published'
            //',start,stop'
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
         //'cssClass'    => &$GLOBALS['TL_DCA']['tl_page']['fields']['cssClass'],
         //TODO:add fields below also to table since this info is truly locale specific
         // 'robots'      => &$GLOBALS['TL_DCA']['tl_page']['fields']['robots'],
         'dateFormat'  => &$GLOBALS['TL_DCA']['tl_page']['fields']['dateFormat'],
         'timeFormat'  => &$GLOBALS['TL_DCA']['tl_page']['fields']['timeFormat'],
         'datimFormat' => &$GLOBALS['TL_DCA']['tl_page']['fields']['datimFormat'],
         'sitemapName' => &$GLOBALS['TL_DCA']['tl_page']['fields']['sitemapName'],
         'published'   => &$GLOBALS['TL_DCA']['tl_page']['fields']['published'],
         'start'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['start'],
         'stop'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['stop']
         );

$i18nl10n_languages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);
foreach($i18nl10n_languages as $k=>$v){
    if($v==$GLOBALS['TL_CONFIG']['i18nl10n_default_language']) {
        $i18nl10n_languages = array_delete($i18nl10n_languages,$k);
        break;
    }
}


$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['language'] = array_merge(
        $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
        array(
            'label'     => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
             'filter'   => true,
            'inputType' => 'select',
            'options'   => $i18nl10n_languages,
            'reference'  => &$GLOBALS['TL_LANG']['LNG'])
);
//var_export($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['title']);
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

}//end class tl_page_i18nl10n
?>