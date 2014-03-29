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
