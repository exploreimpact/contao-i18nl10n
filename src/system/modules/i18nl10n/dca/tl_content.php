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
 * @version     1.2.0.rc
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
    public function appendLanguageInput(DataContainer $dc = null)
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

    /**
     * Create buttons for languages with user/group permission
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     *
     * @return string
     */
    public function hideButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes) {
        $strLanguageIdentifier = $this->getLanguageIdentifierByElementRow($arrRow);

        return $this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages)
            ? $this->createListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes)
            : '';
    }

    /**
     * Create delete button for languages with user/group permission
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     *
     * @return string
     */
    public function deleteButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes)
    {
        $strLanguageIdentifier = $this->getLanguageIdentifierByElementRow($arrRow);

        if ($this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages)) {
            $objCallback = new tl_content();
            return $objCallback->deleteElement($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes);
        }

        return '';
    }

    /**
     * Create toggle button for languages with user/group permission
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     *
     * @return string
     */
    public function toggleButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes)
    {
        $strLanguageIdentifier = $this->getLanguageIdentifierByElementRow($arrRow);

        if ($this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages)) {
            $objCallback = new tl_content();
            return $objCallback->toggleIcon($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes);
        }

        return '';
    }

    /**
     * Create buttons for languages with user/group permission with vendor module support
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     * @param $strTable
     * @param $arrRootIds
     * @param $arrChildRecordIds
     * @param $blnCircularReference
     * @param $strPrevious
     * @param $strNext
     * @param $dc
     *
     * @return string
     */
    public function hideButtonVendorSupport($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc)
    {
        $strLanguageIdentifier = $this->getLanguageIdentifierByElementRow($arrRow);
        $return = '';

        // If is allowed to edit language, create icon string
        if ($this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages)) {
            $strButton = $this->createVendorListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc);

            $return = $strButton !== false
                ? $strButton
                : $this->createListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes);
        }

        return $return;
    }

    /**
     * Create delete button for languages with user/group permission with vendor module support
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     * @param $strTable
     * @param $arrRootIds
     * @param $arrChildRecordIds
     * @param $blnCircularReference
     * @param $strPrevious
     * @param $strNext
     * @param $dc
     *
     * @return bool|string
     */
    public function deleteButtonVendorSupport($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc)
    {
        $strLanguageIdentifier = $this->getLanguageIdentifierByElementRow($arrRow);
        $return = '';

        // If is allowed to edit language, create icon string
        if ($this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages)) {
            $return = $this->createVendorListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc);

            // If button create failed, create it now
            if ($return === false) {
                $objCallback = new tl_content();
                $return = $objCallback->deleteElement($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes);
            }
        }

        return $return;
    }

    /**
     * Create toggle button for languages with user/group permission with vendor module support
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     * @param $strTable
     * @param $arrRootIds
     * @param $arrChildRecordIds
     * @param $blnCircularReference
     * @param $strPrevious
     * @param $strNext
     * @param $dc
     *
     * @return bool|string
     */
    public function toggleButtonVendorSupport($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc)
    {
        $strLanguageIdentifier = $this->getLanguageIdentifierByElementRow($arrRow);
        $return = '';

        // If is allowed to edit language, create icon string
        if ($this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages)) {
            $return = $this->createVendorListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc);

            if ($return === false) {
                $objCallback = new tl_content();
                $return = $objCallback->toggleIcon($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes);
            }
        }

        return $return;
    }

    /**
     * Create language identifier
     *
     * Get an identifier string of combined root page id and language
     * based on content element
     *
     * @param $arrRow
     *
     * @return string
     */
    private function getLanguageIdentifierByElementRow($arrRow)
    {
        $objArticle = \ArticleModel::findByPk($arrRow['pid']);
        $objPage = \PageModel::findWithDetails($objArticle->pid);

        return $objPage->rootId . '::' . $arrRow['language'];
    }

    /**
     * Create button
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     *
     * @return string
     */
    private function createListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes)
    {
        return '<a href="' . $this->addToUrl($strHref . '&amp;id=' . $arrRow['id']) . '" title="' . specialchars($strTitle) . '"' . $arrAttributes . '>' . \Image::getHtml($strIcon, $strLabel) . '</a> ';
    }

    /**
     * Call a the vendor callback backup
     *
     * @param $arrRow
     * @param $strHref
     * @param $strLabel
     * @param $strTitle
     * @param $strIcon
     * @param $arrAttributes
     * @param $strTable
     * @param $arrRootIds
     * @param $arrChildRecordIds
     * @param $blnCircularReference
     * @param $strPrevious
     * @param $strNext
     * @param $dc
     *
     * @return string|bool  If something went wrong, 'false' is returned
     */
    private function createVendorListButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc)
    {
        $arrAct = explode('=', $strHref);
        $return = false;

        $arrVendorCallback = $GLOBALS['TL_DCA'][$strTable]['list']['operations'][$arrAct[1]]['i18nl10n_button_callback'];

        // Call vendor callback
        if (is_array($arrVendorCallback)) {
            $this->import($arrVendorCallback[0]);
            $return = $this->$arrVendorCallback[0]->$arrVendorCallback[1]($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc);
        } elseif (is_callable($arrVendorCallback)) {
            $return = $arrVendorCallback($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $arrAttributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext, $dc);
        }

        return $return;
    }

}
