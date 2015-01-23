<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n dca
 * @version     1.2.1
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

use Verstaerker\I18nl10n\Classes\I18nl10n;

$this->loadLanguageFile('languages');
$this->loadLanguageFile('tl_page');
$this->loadLanguageFile('tl_content');
$this->loadDataContainer('tl_page');
$this->loadDataContainer('tl_content');

// set callback for dca load to add language selection to content elements IF module is article
if (\Input::get('do') == 'article') {
    // define callback to add language icons
    $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] =
        array('tl_content_l10n', 'addCteType');
}

$GLOBALS['TL_DCA']['tl_content']['fields']['language'] = array_merge(
    $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
    array(
        'label'            => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => array('tl_content_l10n', 'languageOptions'),
        'reference'        => &$GLOBALS['TL_LANG']['LNG'],
        'eval'             => array(
            'mandatory'          => false,
            'includeBlankOption' => true,
            'blankOptionLabel'   => $GLOBALS['TL_LANG']['tl_content']['i18nl10n_blankOptionLabel'],
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
     *
     * @return string
     */
    public function addCteType($arrRow)
    {
        // Prepare icon link
        $langIcon = $arrRow['language']
            ? 'system/modules/i18nl10n/assets/img/flag_icons/' . $arrRow['language'] . '.png'
            : 'system/modules/i18nl10n/assets/img/i18nl10n.png';

        // create l10n information insert
        $strL10nInsert = sprintf(
            '<img class="i18nl10n_content_flag" src="%1$s" /> [%2$s] ',
            $langIcon,
            $GLOBALS['TL_LANG']['LNG'][$arrRow['language']]
        );

        // get html string from Contao
        $strElement = parent::addCteType($arrRow);
        $strRegex   = '@(.*?class="cte_type.*?>)(.*)@m';

        // splice in l10n information
        $strElement = preg_replace($strRegex, '${1}' . $strL10nInsert . '${2}', $strElement);

        return $strElement;
    }


    /**
     * Onload callback for tl_content
     *
     * Add language field to all content palettes
     *
     * @param DataContainer $dc
     */
    public function onLoadCallback(DataContainer $dc = null)
    {
        $this->loadLanguageFile('tl_content');
        $dc->loadDataContainer('tl_page');
        $dc->loadDataContainer('tl_content');

        // add language section to all palettes
        foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $k => $v) {
            // if element is '__selector__' OR 'default' OR the palette has already a language key
            if ($k == '__selector__' || $k == 'default' || strpos($v, ',language(?=\{|,|;|$)') !== false) {
                continue;
            }

            $GLOBALS['TL_DCA']['tl_content']['palettes'][$k] = $v . ';{i18nl10n_legend:hide},language';
        }
    }

    /**
     * Create language options based on root page and already used languages
     *
     * @param DataContainer $dc
     *
     * @return array
     */
    public function languageOptions(DataContainer $dc)
    {
        $arrLanguages = $GLOBALS['TL_LANG']['LNG'];
        $arrOptions   = array();

        $i18nl10nLanguages = $this->getLanguageOptionsByContentId($dc->activeRecord->id);

        // Create options array base on root page languages
        foreach ($i18nl10nLanguages as $language) {
            $arrOptions[$language] = $arrLanguages[$language];
        }

        return $arrOptions;
    }

    /**
     * Get available languages by content element id
     *
     * @param $id
     *
     * @return array
     */
    private function getLanguageOptionsByContentId($id)
    {
        $arrPageId = $this->Database
            ->prepare("SELECT pid as id FROM tl_article WHERE id = (SELECT pid FROM tl_content WHERE id = ?)")
            ->limit(1)
            ->execute($id)
            ->fetchAssoc();

        $i18nl10nLanguages = I18nl10n::getLanguagesByPageId($arrPageId['id'], 'tl_page');

        return $i18nl10nLanguages['languages'];
    }

}