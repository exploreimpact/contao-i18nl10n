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

// set callback for dca load to add language selection to content elements IF module is article
if (\Input::get('do') == 'article')
{
    $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_l10n', 'onLoadCallback');
}

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
        $langIcon = 'system/modules/i18nl10n/assets/img/i18nl10n.png';

        if ($arrRow['language'])
        {
            $langIcon = 'system/modules/i18nl10n/assets/img/flag_icons/' . $arrRow['language'] . '.png';
        }

        $strL10nInsert = '<img class="i18nl10n_content_flag" src="%1$s" /> [%2$s] ';

        // create l10n information insert
        $strL10nInsert = sprintf(
            $strL10nInsert,
            $langIcon,
            $GLOBALS['TL_LANG']['LNG'][$arrRow['language']]
        );

        // get html string from Contao
        $strElement = parent::addCteType($arrRow);
        $strRegex = '@(.*?class="cte_type.*?>)(.*)@m';

        // splice in l10n information
        $strElement = preg_replace($strRegex, '${1}' . $strL10nInsert . '${2}', $strElement);

        return $strElement;
    }


    /**
     * Onload callback for tl_content
     *
     * Add language field to all content palettes
     *
     * @param \DataContainer $dc
     */
    public function onLoadCallback(\DataContainer $dc = null) {


        $this->loadLanguageFile('tl_content');
        $dc->loadDataContainer('tl_page');
        $dc->loadDataContainer('tl_content');

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

}