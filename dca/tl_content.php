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
$this->loadLanguageFile('tl_page');
$this->loadLanguageFile('tl_content');
$this->loadDataContainer('tl_page');
$this->loadDataContainer('tl_content');
$site_langs = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);
array_unshift($site_langs,'');

$GLOBALS['TL_DCA']['tl_content']['fields']['language'] = array_merge(
    $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
    array(
        'label'     => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
        'filter'    => true,
        'default'   =>'',
        'inputType' => 'select',
        'options'   => $site_langs,
        'reference'  => &$GLOBALS['TL_LANG']['LNG'],
        'eval'      => array('mandatory'=>false, 
             'rgxp'=>'alpha', 'maxlength'=>2, 
             'nospace'=>true, 'tl_class'=>'w50')
        )
);

//add language field to all palletes
foreach($GLOBALS['TL_DCA']['tl_content']['palettes'] as $k => $v){
    if( $k == '__selector__' ) continue;
    $GLOBALS['TL_DCA']['tl_content']['palettes'][$k] = "$v;".'{l10n_legend:hide},language;';
}
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] =
    array('tl_content_l10ns','addCteType');


class tl_content_l10ns extends tl_content {
    //Hm.. extended but again -> copy/paste/modify... A preg_replace on the
    //return of parent::addCteType seems more ...elegant?!?!
    public function addCteType($arrRow) {
        $key = $arrRow['invisible'] ? 'unpublished' : 'published';
        $l10n_string = ($arrRow['language']?
                        '<img style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/flag_icons/png/'
                .$arrRow['language'].'.png" /> ['
                .$GLOBALS['TL_LANG']['LNG'][$arrRow['language']].'] ':'
                <img style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/icon.png" /> ['
                .$GLOBALS['TL_LANG']['LNG'][$arrRow['language']].']
                '
                        );
        return '
<div class="cte_type ' . $key . '">' . $l10n_string
. $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] . (($arrRow['type'] == 'alias') ? ' ID ' . $arrRow['cteAlias'] : '') . ($arrRow['protected'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['protected'] . ')' : ($arrRow['guests'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['guests'] . ')' : '')) . '</div>
<div class="limit_height' . (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : '') . ' block">
' . $this->getContentElement($arrRow['id']) . '
</div>' . "\n";
    }

}
?>
