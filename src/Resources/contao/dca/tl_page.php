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
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

use Verstaerker\I18nl10nBundle\Classes\I18nl10n;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

// load language translations
$this->loadLanguageFile('languages');

/**
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['page_i18nl10n'] = array
(
    'label'           => 'L10N',
    'href'            => 'do=i18nl10n',
    'button_callback' => array('tl_page_l10n', 'editL10n')
);

// Extend onload_callback
\array_insert(
    $GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'],
    count($GLOBALS['TL_DCA']['tl_page']['config']['onload_callback']) - 1,
    [
        ['tl_page_l10n', 'setDefaultLanguage'],
        ['tl_page_l10n', 'displayDnsMessage'],
        ['tl_page_l10n', 'addI18nl10nPublishedField'],
        ['tl_page_l10n', 'setDnsMandatory'],
        ['tl_page_l10n', 'extendRootPalettes'],
    ]
);

// Extend onsubmit_callback
\array_insert(
    $GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'],
    count($GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback']) - 1,
    [
        array('tl_page_l10n', 'generatePageL10n'),
        array('tl_page_l10n', 'updateDefaultLanguage'),
    ]
);

// Extend ondelete_callback
$GLOBALS['TL_DCA']['tl_page']['config']['ondelete_callback'][] = ['tl_page_l10n', 'onDelete'];

// Extend oncopy_callback
$GLOBALS['TL_DCA']['tl_page']['config']['oncopy_callback'][] = ['tl_page_l10n', 'onCopy'];

// Insert i18nl10n fields
\array_insert(
    $GLOBALS['TL_DCA']['tl_page']['fields'],
    \count($GLOBALS['TL_DCA']['tl_page']['fields']),
    [
        'i18nl10n_published' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_page']['i18nl10n_published'],
            'default'           => true,
            'exclude'           => true,
            'inputType'         => 'checkbox',
            'eval' => [
                'doNotCopy'     => true,
                'tl_class'      => 'w50',
            ],
            'sql'               => "char(1) NOT NULL default '1'",
        ],
        'i18nl10n_localizations' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_page']['i18nl10n_localizations'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'w50 autoheight',
                'columnFields' => [
                    'language' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_page']['i18nl10n_language'],
                        'exclude'   => true,
                        'inputType' => 'select',
                        'options_callback' => ['tl_page_l10n', 'languageOptions'],
                        'eval'      => [
                            'style'  => 'width:100%',
                            'chosen' => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                ],
            ],
            'save_callback' => [
                ['tl_page_l10n', 'validateLocalizations'],
            ],
            'sql'       => "blob NULL",
        ],
    ]
);


/**
 * Class tl_page_l10n
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     tl_page
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */
class tl_page_l10n extends tl_page
{
    protected $i18nl10nLocalizations;

    /**
     * Add language hyperlink button to entry buttons
     *
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     *
     * @return string
     */
    public function editL10n($row, $href, $label, $title, $icon)
    {
        $strTitle = sprintf(
            $GLOBALS['TL_LANG']['MSC']['editL10n'],
            "\"{$row['title']}\""
        );

        $strButtonUrl = $this->addToUrl($href . '&amp;node=' . $row['id']);

        // Select icon by localization publishing status
        $strImgName = $row['i18nl10n_published'] ? 'i18nl10n.png' : 'i18nl10n_invisible.png';

        return sprintf(
            '<a href="%1$s" title="%2$s"><img src="bundles/verstaerkeri18nl10n/img/%3$s"></a>',
            $strButtonUrl,
            \Contao\StringUtil::specialchars($strTitle),
            $strImgName
        );
    }

    /**
     * Apply the root page language to new pages
     *
     * @returns void
     */
    public function setDefaultLanguage()
    {
        if (\Contao\Input::get('act') != 'create') {
            return;
        }

        if (\Contao\Input::get('pid') != 0) {
            $objPage = \Contao\PageModel::findWithDetails(\Contao\Input::get('pid'));
            $GLOBALS['TL_DCA']['tl_page']['fields']['language']['default'] = $objPage->rootLanguage;
        }
    }

    /**
     * If this installation has more than one root page, check if they all have a domain
     *
     * @return void
     */
    public function displayDnsMessage()
    {
        // Only apply if multiple root pages
        if (I18nl10n::getInstance()->countRootPages() > 1) {
            /** @var \Contao\Database\Result $objRootPages */
            $objRootPages = I18nl10n::getInstance()->getAllRootPages();
            $arrDns = array();

            while ($objRootPages->next()) {

                // Check if dns value is missing
                if ($objRootPages->dns === '') {
                    \Contao\Message::addError($GLOBALS['TL_LANG']['tl_page']['msg_missing_dns']);
                } else {
                    $arrDns[] = $objRootPages->dns;
                }
            }

            // Check if duplicated dns values
            if(count(array_unique($arrDns)) < count($arrDns)) {
                \Contao\Message::addError($GLOBALS['TL_LANG']['tl_page']['msg_duplicated_dns']);
            }
        }
    }

    /**
     * Automatically create a new localization upon page creation
     * (triggered by on submit callback)
     *
     * @param \Contao\DataContainer $dc
     */
    public function generatePageL10n(\Contao\DataContainer $dc)
    {
        // Only continue if new entry
        if (!$this->isNewEntry($dc)) {
            return;
        }

        if ($dc->activeRecord->type !== 'root') {
            $arrI18nl10nLanguages = I18nl10n::getInstance()->getLanguagesByPageId($dc->activeRecord->pid, 'tl_page', true);
            $arrLocalizations = $arrI18nl10nLanguages['localizations'];
        } else {
            // Flatten localizations
            $arrLocalizations = array_map(
                function($value) {
                    return $value['language'];
                },
                \Contao\StringUtil::deserialize($dc->activeRecord->i18nl10n_localizations)
            );
        }

        // If folder urls are enabled, get only last part from alias
        if (\Contao\Config::get('folderUrl')) {
            $arrAlias = explode('/', $dc->activeRecord->alias);
            $strAlias = array_pop($arrAlias);
        } else {
            $strAlias = $dc->activeRecord->alias;
        }

        $fields = array(
            'pid'                => $dc->id,
            'sorting'            => 0,
            'tstamp'             => time(),
            'title'              => $dc->activeRecord->title,
            'pageTitle'          => $dc->activeRecord->pageTitle,
            'description'        => $dc->activeRecord->description,
            'cssClass'           => $dc->activeRecord->cssClass,
            'i18nl10n_published' => $dc->activeRecord->published,
            'start'              => $dc->activeRecord->start,
            'stop'               => $dc->activeRecord->stop,
            'dateFormat'         => $dc->activeRecord->dateFormat,
            'timeFormat'         => $dc->activeRecord->timeFormat,
            'datimFormat'        => $dc->activeRecord->datimFormat
        );

        // Now make copies in each language
        foreach ($arrLocalizations as $language) {
            $strFolderUrl = '';
            $fields['sorting'] += 128;
            $fields['language'] = $language;

            // Create alias based on folder url setting
            if (\Contao\Config::get('folderUrl')) {
                // Get translation for parent page
                $objL10nParentPage = I18nl10n::getInstance()->findL10nWithDetails($dc->activeRecord->pid, $language);

                if ($objL10nParentPage->type !== 'root') {
                    // Create folder url
                    $strFolderUrl = $objL10nParentPage->alias . '/';
                }

            }

            $fields['alias'] = $strFolderUrl . $strAlias . '-' . $dc->activeRecord->pid . $dc->id;

            \Contao\Database::getInstance()
                ->prepare('INSERT INTO tl_page_i18nl10n %s')
                ->set($fields)
                ->execute();
        }
    }

    /**
     * Delete localizations for deleted page
     *
     * @param \Contao\DataContainer $dc
     */
    public function onDelete(\Contao\DataContainer $dc)
    {
        $arrChildRecords = $this->Database->getChildRecords(array($dc->id), 'tl_page');

        // add actual page itself
        $arrChildRecords[] = $dc->id;

        // Delete all related localizations from tl_page_i18nl10n
        $this->Database
            ->prepare('DELETE FROM tl_page_i18nl10n WHERE pid IN(' . implode(',', $arrChildRecords) . ')')
            ->execute();
    }

    /**
     * OnCopy callback function
     *
     * Set page language on copy
     *
     * @param $intId
     * @param \Contao\DataContainer $dc
     */
    public function onCopy($intId, \Contao\DataContainer $dc)
    {
        $objPage = \Contao\PageModel::findWithDetails($intId);

        $this->Database
            ->prepare('UPDATE tl_page SET language = ? WHERE id = ?')
            ->execute($objPage->language, $intId);
    }

    /**
     * Update child pages of saved root with default language
     *
     * @param \Contao\DataContainer $dc
     */
    public function updateDefaultLanguage(\Contao\DataContainer $dc)
    {

        if ($dc->activeRecord->type != 'root') {
            return;
        }

        $arrChildRecords = $this->Database
            ->getChildRecords(array($dc->activeRecord->id), 'tl_page');

        if (count($arrChildRecords)) {
            $this->Database
                ->prepare('UPDATE tl_page SET language = ? WHERE id IN(' . implode(',', $arrChildRecords) . ')')
                ->execute($dc->activeRecord->language);
        }
    }

    /**
     * Check if given language code is valid and available
     *
     * @param string $strLanguage
     *
     * @return bool
     */
    public function isValidLanguageCode($strLanguage)
    {
        return \array_key_exists($strLanguage, $GLOBALS['TL_LANG']['LNG']);
    }

    /**
     * Check if the given data container is a new entry
     *
     * @param \Contao\DataContainer $dc
     *
     * @return bool
     */
    public function isNewEntry(\Contao\DataContainer $dc)
    {
        $objPage = $this->Database
            ->prepare('SELECT * FROM tl_page WHERE id = ?')
            ->limit(1)
            ->execute($dc->id);

        return $objPage->first()->tstamp == 0;
    }

    /**
     * Map language options
     *
     * @return array
     */
    public function languageOptions() {
        $arrLanguages = $GLOBALS['TL_LANG']['LNG'];

        // Remove 'all' entry
        unset($arrLanguages['']);

        // Change format from underscores to hyphens
        foreach ($arrLanguages as $short => $name) {
            if (\strpos($short, '_')) {
                unset($arrLanguages[$short]);
                $arrLanguages[
                    \str_replace('_', '-', $short)
                ] = $name;
            }
        }

        // Sort by value (a-z)
        \asort($arrLanguages);

        return $arrLanguages;
    }

    /**
     * Validate localizations
     *
     * Remove duplicates
     * Remove root language
     *
     * @param string                $strValue
     * @param \Contao\DataContainer $dc
     *
     * @return string
     */
    public function validateLocalizations($strValue, \Contao\DataContainer $dc)
    {
        $arrLanguages = array();
        $arrValues = \Contao\StringUtil::deserialize($strValue);
        $strRootLanguage = $dc->activeRecord->language;

        foreach ($arrValues as $key => $value) {
            // Remove empty OR duplicates OR root language
            if (empty($value['language'])
                || in_array($value['language'], $arrLanguages)
                || $value['language'] === $strRootLanguage) {
                \array_splice($arrValues, $key, 1);
                continue;
            }

            $arrLanguages[] = $value['language'];
        }

        return serialize($arrValues);
    }

    /**
     * Extend tl_page palette on onload_callback
     *
     * Extend palette with i18nl10n_published IF not a root page
     *
     * @see DC_File::__construct
     */
    public function addI18nl10nPublishedField()
    {
        $intId = \Contao\Input::get('id');

        if ($intId) {
            $arrResult = \Contao\Database::getInstance()
                ->prepare('SELECT pid FROM tl_page WHERE id = ?')
                ->limit(1)
                ->execute($intId)
                ->fetchAssoc();

            // Check if element has parent and therefore is no root page
            if (!empty($arrResult) && intval($arrResult['pid']) !== 0) {
                // Add i18nl10n_published field
                foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $k => $v) {
                    if ('__selector__' == $k) {
                        continue;
                    }

                    PaletteManipulator::create()
                        ->addField('i18nl10n_published', 'published')
                        ->applyToPalette($k, 'tl_page');
                }

                // Update field class
                $GLOBALS['TL_DCA']['tl_page']['fields']['published']['eval']['tl_class'] = 'w50';
            }
        }
    }

    /**
     * Set dns field eval to mandatory
     *
     * @see DC_File::__construct
     */
    public function setDnsMandatory()
    {
        if (I18nl10n::getInstance()->countRootPages() > 1) {
            // If there is already a root page, a domain name must be set
            $GLOBALS['TL_DCA']['tl_page']['fields']['dns']['eval']['mandatory'] = true;
        }
    }

    /**
     * Extend root palettes on onload_callback
     *
     * @see DC_File::__construct
     */
    public function extendRootPalettes()
    {
        PaletteManipulator::create()
            ->addLegend('module_i18nl10n', 'dns_legend', PaletteManipulator::POSITION_AFTER)
            ->addField('i18nl10n_localizations', 'module_i18nl10n', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('root', 'tl_page')
            ->applyToPalette('rootfallback', 'tl_page');
    }

    /**
     * Create list button on button_callback
     *
     * @param   string  $strOperation
     * @param   array   $arrArgs
     * @param   array|null   $arrVendorCallback
     *
     * @return  string
     */
    public function createButton($strOperation, $arrArgs, $arrVendorCallback = null)
    {
        // If is allowed to edit language, create icon string
        if ($this->User->isAdmin || $this->userHasPermissionToEditLanguage($arrArgs[0])) {
            $strButton = $this->createVendorListButton($arrVendorCallback, $arrArgs);

            if( $strButton !== false ) {
                return $strButton;
            }

            switch ($strOperation) {
                case 'delete':
                    $callback = 'deleteElement';
                    break;

                case 'toggle':
                    $callback = 'toggleIcon';
                    break;

                default:
                    $callback = 'createListButton';
            }

            return call_user_func_array(array($this, $callback), $arrArgs);
        }

        return '';
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
        return '<a href="' . $this->addToUrl($strHref . '&amp;id=' . $arrRow['id']) . '" title="' . \Contao\StringUtil::specialchars($strTitle) . '" ' . $arrAttributes . '>' . \Contao\Image::getHtml($strIcon, $strLabel) . '</a> ';
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
            $return = \call_user_func_array(array($vendorClass, $arrVendorCallback[1]), $arrArgs);
        } elseif (is_callable($arrVendorCallback)) {
            $return = \call_user_func_array($arrVendorCallback, $arrArgs);
        }

        return $return;
    }

    /**
     * Check if current user has permission to edit given page
     *
     * @param $arrRow
     *
     * @return bool
     */
    private function userHasPermissionToEditLanguage($arrRow)
    {
        $objPage = \Contao\PageModel::findWithDetails($arrRow['id']);
        $strLanguageIdentifier = $objPage->rootId . '::' . $arrRow['language'];

        return in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages);
    }
}
