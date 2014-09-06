<?php

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

$GLOBALS['TL_DCA']['tl_content']['fields']['language'] = array_merge(
    $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
    array(
        'label'     => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
        'filter'    => true,
        'inputType' => 'select',
        'options'   => deserialize(\Config::get('i18nl10n_languages')),
        'reference' => &$GLOBALS['TL_LANG']['LNG'],
        'eval'      => array(
            'mandatory'          => false,
            'includeBlankOption' => true,
            'blankOptionLabel'   => $GLOBALS['TL_LANG']['tl_content']['l10n_blankOptionLabel'],
            'rgxp'               => 'alpha',
            'maxlength'          => 2,
            'nospace'            => true,
            'tl_class'           => 'w50'
        )
    )
);


// only insert language extension in article module
if (\Input::get('do') == 'article')
{
    // add language section to all palettes
    foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $k => $v)
    {
        if ($k == '__selector__') continue;
        $GLOBALS['TL_DCA']['tl_content']['palettes'][$k] = "$v;" . '{l10n_legend:hide},language;';
    }

    // define callback to add language icons
    $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] =
        array('tl_content_l10n', 'addCteType');
}


class tl_content_l10n extends tl_content
{

    /**
     * Add language icon to content element list entry
     *
     * @param $arrRow
     * @return string
     */
    public function addCteType($arrRow)
    {
        $key = $arrRow['invisible'] ? 'unpublished' : 'published';
        $langIcon = 'system/modules/i18nl10n/assets/img/i18nl10n.png';

        if ($arrRow['language'])
        {
            $langIcon = 'system/modules/i18nl10n/assets/img/flag_icons/' . $arrRow['language'] . '.png';
        }

        $html = '<div class="cte_type %1$s"><img class="i18nl10n_content_flag" src="%2$s" /> [%3$s] %4$s %5$s';

        if ($arrRow['protected'])
        {
            $html .= ' (%6$s)';
        }
        elseif ($arrRow['guests'])
        {
            $html .= ' (%7$s)';
        }

        $html .= '</div><div class="limit_height %8$s block">%9$s</div>' . "\n";

        return sprintf(
            $html,
            $key,
            $langIcon,
            $GLOBALS['TL_LANG']['LNG'][$arrRow['language']],
            $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0],
            $arrRow['type'] == 'alias' ? 'ID ' . $arrRow['cteAlias'] : '',
            $GLOBALS['TL_LANG']['MSC']['protected'],
            $GLOBALS['TL_LANG']['MSC']['guests'],
            \Config::get('doNotCollapse') ? '' : 'h64',
            $this->getContentElement($arrRow['id'])
        );
    }

}