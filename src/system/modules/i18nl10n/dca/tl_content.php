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
     * - Filter by permission
     * - Add a blank option (by permission)
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

        $i18nl10nLanguages = I18nl10n::getInstance()->getLanguagesByPageId($arrPageId['id'], 'tl_page');
        $strIdentifier = $i18nl10nLanguages['rootId'] . '::';

        // Create base and add neutral (*) language if admin or has permission
        $arrOptions = $this->User->isAdmin || in_array($strIdentifier . '*', (array) $this->User->i18nl10n_languages)
              ? array('')
              : array();

        // Add languages based on permissions
        if ($this->User->isAdmin) {
            array_insert($arrOptions, 1, $i18nl10nLanguages['languages']);
        } else {
            foreach ($i18nl10nLanguages['languages'] as $language) {
                if (in_array($strIdentifier . $language, (array) $this->User->i18nl10n_languages)) {
                    $arrOptions[] = $language;
                }
            }
        }

        return $arrOptions;
    }

    /**
     * Create buttons for languages with user/group permission with vendor module support
     *
     * @param   array   $strOperation      operation name of button
     * @param   array   $arrVendorCallback
     * @param   array   $arrArgs           {row, href, label, title, icon, attributes, table, rootIds, childRecordIds, circularReference, previous, next, dc}
     *
     * @return  string
     */
    public function createButton($strOperation, $arrVendorCallback = null, $arrArgs)
    {
        $return = '';

        // If is allowed to edit language, create icon string
        if ($this->User->isAdmin || $this->userHasPermissionToEditLanguage($arrArgs[0])) {
            $strButton = $this->createVendorListButton($arrVendorCallback, $arrArgs);

            switch ($strOperation) {
                case 'delete':
                    $return = $strButton === false
                        ? call_user_func_array(array($this, 'deleteElement'), $arrArgs)
                        : $strButton;
                    break;

                case 'toggle':
                    $return = $strButton === false
                        ? call_user_func_array(array($this, 'toggleIcon'), $arrArgs)
                        : $strButton;
                    break;

                default:
                    $return = $strButton === false
                        ? call_user_func_array(array($this, 'createListButton'), $arrArgs)
                        : $strButton;
            }

        }

        return $return;
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
     * @param   $arrVendorCallback
     * @param   $arrArgs            {row, href, label, title, icon, attributes, table, rootIds, childRecordIds, circularReference, previous, next, dc}
     *
     * @return string|bool  If something went wrong, 'false' is returned
     */
    private function createVendorListButton($arrVendorCallback = null, $arrArgs)
    {
        $return = false;

        // Call vendor callback
        if (is_array($arrVendorCallback)) {
            $vendorClass = new $arrVendorCallback[0];
            $return = call_user_func_array(array($vendorClass, $arrVendorCallback[1]), $arrArgs);
        } elseif (is_callable($arrVendorCallback)) {
            $return = call_user_func_array($arrVendorCallback, $arrArgs);
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
     * @return boolean
     */
    private function userHasPermissionToEditLanguage($arrRow)
    {
        // @todo: check for tid like tl_page_i18nl10n.605

        $objArticle = \ArticleModel::findByPk($arrRow['pid']);
        $objPage = \PageModel::findWithDetails($objArticle->pid);
        $strLanguage = !empty($arrRow['language'])
            ? $arrRow['language']
            : '*';

        $strLanguageIdentifier = $objPage->rootId . '::' . $strLanguage;

        return in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages);
    }

}
