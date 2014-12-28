<?php

/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 * @copyright   Verstärker, Patric Eberle 2014
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

use Verstaerker\I18nl10n\Classes\I18nl10n;

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

$onLoadCallback = array
(
    array('tl_page_l10n', 'setDefaultLanguage'),
    array('tl_page_l10n', 'displayLanguageMessage')
);

array_insert(
    $GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'],
    count($GLOBALS['TL_DCA']['tl_page']['config']['onload_callback']) - 1,
    $onLoadCallback
);

$onSubmitCallback = array
(
    array('tl_page_l10n', 'generatePageL10n'),
    array('tl_page_l10n', 'updateDefaultLanguage')
);

array_insert(
    $GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'],
    count($GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback']) - 1,
    $onSubmitCallback
);

$GLOBALS['TL_DCA']['tl_page']['config']['ondelete_callback'][] = array
(
    'tl_page_l10n',
    'onDelete'
);

/**
 * Append l10n published field to palette (if NOT root page)
 */
if (\Input::get('pid') === null || \Input::get('pid') != 0) {

    // switch field splicing based on Contao version
    if (isset($GLOBALS['TL_DCA']['tl_page']['subpalettes']['published'])) {
        // is Contao 3.4+
        $GLOBALS['TL_DCA']['tl_page']['subpalettes']['published'] =
            'l10n_published,' . $GLOBALS['TL_DCA']['tl_page']['subpalettes']['published'];
    } else {
        // is before Contao 3.4
        foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $k => $v) {
            $GLOBALS['TL_DCA']['tl_page']['palettes'][$k] = str_replace('published,', 'published,l10n_published,', $v);
        }
    }

    // update field class
    $GLOBALS['TL_DCA']['tl_page']['fields']['published']['eval']['tl_class'] = 'w50';
}

$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace(
    'language,fallback;',
    'language,fallback;{i18nl10n},i18nl10n_languages;',
    $GLOBALS['TL_DCA']['tl_page']['palettes']['root']
);

/**
 * Define i18nl10n fields
 */
$i18nl10nFields = array(
    'l10n_published'     => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['l10n_published'],
        'default'   => true,
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array(
            'doNotCopy' => true,
            'tl_class'  => 'w50'
        ),
        'sql'       => "char(1) NOT NULL default '1'"
    ),
    'i18nl10n_languages' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['i18nl10n_languages'],
        'exclude'   => true,
        'inputType' => 'multiColumnWizard',
        'eval'      => array
        (
            'tl_class'     => 'w50 autoheight',
            'columnFields' => array
            (
                'language' => array
                (
                    'label'     => &$GLOBALS['TL_LANG']['tl_page']['i18nl10n_language'],
                    'exclude'   => true,
                    'inputType' => 'select',
                    'options'   => &$GLOBALS['TL_LANG']['LNG'],
                    'eval'      => array
                    (
                        'style'  => 'width:250px',
                        'chosen' => true
                    )
                )
            )
        ),
        'sql'       => "blob NULL"
    )
);

/**
 * Insert i18nl10n fields
 */
array_insert(
    $GLOBALS['TL_DCA']['tl_page']['fields'],
    count($GLOBALS['TL_DCA']['tl_page']['fields']),
    $i18nl10nFields
);


/**
 * Class tl_page_l10n
 *
 * @copyright   Verstärker, Patric Eberle 2014
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     tl_page
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */
class tl_page_l10n extends tl_page
{

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
        $title = sprintf(
            $GLOBALS['TL_LANG']['MSC']['editL10n'],
            "\"{$row['title']}\""
        );

        $buttonURL = $this->addToUrl($href . '&amp;node=' . $row['id']);

        return sprintf(
            '<a href="%1$s" title="%2$s"><img src="system/modules/i18nl10n/assets/img/i18nl10n.png"></a>',
            $buttonURL,
            specialchars($title)
        );
    }

    /**
     * Apply the root page language to new pages
     *
     * @returns void
     */
    public function setDefaultLanguage()
    {
        if (\Input::get('act') != 'create') {
            return;
        }

        if (\Input::get('pid') != 0) {
            $objPage = \PageModel::findWithDetails(\Input::get('pid'));
            $GLOBALS['TL_DCA']['tl_page']['fields']['language']['default'] = $objPage->rootLanguage;
        }
    }

    /**
     * Display message when only basic language is available
     *
     * @return  void
     */
    public function displayLanguageMessage()
    {

        // only show if treeview
        if (\Input::get('act') != '') {
            return;
        }

        $i18nl10nLanguages = deserialize(\Config::get('i18nl10n_languages'));

        // if no languages or count is smaller 2 (1 = default language)
        if (!$i18nl10nLanguages || count($i18nl10nLanguages) < 2) {

            $this->loadLanguageFile('tl_page_i18nl10n');

            // TODO: ref= would be nice for link
            $message = sprintf(
                $GLOBALS['TL_LANG']['tl_page']['msg_no_languages'],
                '<a class="tl_message_link" href="contao/main.php?do=settings">',
                '</a>'
            );

            \Message::addError($message);

        };
    }

    /**
     * Automatically create a new localization upon page creation
     * (triggered by on submit callback)
     *
     * @param DataContainer $dc
     */
    public function generatePageL10n(DataContainer $dc)
    {

        if (!$dc->activeRecord || $dc->activeRecord->tstamp > 0) {
            return;
        }

        $new_records = $this->Session->get('new_records');

        // Not a new page - copy/paste is a great way to share code :P
        if (!$new_records
            || is_array($new_records[$dc->table])
               && !in_array($dc->id, $new_records[$dc->table])
        ) {
            return;
        }

        $i18nl10nLanguages = deserialize(\Config::get('i18nl10n_languages'));

        if (Config::get('folderUrl')) {
            $arrAlias = explode('/', $dc->activeRecord->alias);
            $strAlias = array_pop($arrAlias);
        } else {
            $strAlias = $dc->activeRecord->alias;
        }

        $fields = array(
            'pid'            => $dc->id,
            'sorting'        => 0,
            'tstamp'         => time(),
            'title'          => $dc->activeRecord->title,
            'type'           => $dc->activeRecord->type,
            'pageTitle'      => $dc->activeRecord->pageTitle,
            'description'    => $dc->activeRecord->description,
            'cssClass'       => $dc->activeRecord->cssClass,
            'l10n_published' => $dc->activeRecord->published,
            'start'          => $dc->activeRecord->start,
            'stop'           => $dc->activeRecord->stop,
            'dateFormat'     => $dc->activeRecord->dateFormat,
            'timeFormat'     => $dc->activeRecord->timeFormat,
            'datimFormat'    => $dc->activeRecord->datimFormat
        );

        //now make copies in each language.
        foreach ($i18nl10nLanguages as $language) {
            if ($language == \Config::get('i18nl10n_default_language')) {
                continue;
            }

            $strFolderUrl = '';
            $fields['sorting'] += 128;
            $fields['language'] = $language;

            // Create alias based on folder url setting
            if (Config::get('folderUrl')) {
                // Get translation for parent page
                $objL10nParentPage = I18nl10n::findL10nWithDetails($dc->activeRecord->pid, $language);

                if ($objL10nParentPage->type !== 'root') {
                    // Create folder url
                    $strFolderUrl = $objL10nParentPage->alias . '/';
                }

            }

            $fields['alias'] = $strFolderUrl . $strAlias . '-' . $dc->activeRecord->pid . $dc->id;

            $sql = 'INSERT INTO tl_page_i18nl10n %s';

            \Database::getInstance()
                ->prepare($sql)
                ->set($fields)
                ->execute();
        }
    }

    /**
     * Delete localizations for deleted page
     *
     * @param DataContainer $dc
     */
    public function onDelete(DataContainer $dc)
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
     * Set root page language as default language and update available languages
     *
     * @param DataContainer $dc
     */
    public function updateDefaultLanguage(DataContainer $dc)
    {

        if ($dc->activeRecord->type != 'root') {
            return;
        }

        $objFirstChildPage = $this->Database
            ->prepare('SELECT * FROM tl_page WHERE pid = ? && language = ?')
            ->execute($dc->activeRecord->id, $dc->activeRecord->language);

        // If first child has not same language, language needs to be updated on page tree
        if(!$objFirstChildPage->count()) {

            $arrChildRecords = $this->Database
                ->getChildRecords(array($dc->activeRecord->id), 'tl_page');

            $this->Database
                ->prepare('UPDATE tl_page SET language = ? WHERE id IN(' . implode(',', $arrChildRecords) . ')')
                ->execute($dc->activeRecord->language);

        }
    }

    /**
     * Check if given language code is valid and available
     *
     * @param $strLanguage
     *
     * @return bool
     */
    public function isValidLanguageCode($strLanguage)
    {
        return array_key_exists($strLanguage, $GLOBALS['TL_LANG']['LNG']);
    }
}
