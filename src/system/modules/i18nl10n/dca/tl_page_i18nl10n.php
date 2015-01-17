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

// load language translations
$this->loadLanguageFile('languages');

// load tl_page class and translation
$this->loadLanguageFile('tl_page');
$this->loadDataContainer('tl_page');


//determine if languages are available to endable/disable editing
$enableCreate = false;

// Check if backend mode to prevent install issue
if (\Input::get('do') === 'i18nl10n') {
    $arrLanguages = I18nl10n::getAllLanguages();

    // Check if localizations are available, else the create option for the DCA will be disabled
    if (count($arrLanguages)) {
        foreach ($arrLanguages as $domain) {
            if (count($domain['localizations'])) {
                $enableCreate = true;
                break;
            }
        }
    };
}

/**
 * Table tl_page_i18nl10n
 */
$GLOBALS['TL_DCA']['tl_page_i18nl10n'] = array
(
    // Config
    'config'   => array
    (
        'dataContainer'    => 'Table',
        'ptable'           => 'tl_page',
        'enableVersioning' => true,
        'closed'           => !$enableCreate,
        'onload_callback'  => array
        (
            array('tl_page', 'addBreadcrumb'),
            array('tl_page_i18nl10n', 'displayLanguageMessage'),
            array('tl_page_i18nl10n', 'localizeAllHandler'),
            array('tl_page_i18nl10n', 'checkPermission')
        ),
        'sql'              => array
        (
            'keys' => array
            (
                'id'    => 'primary',
                'pid'   => 'index',
                'alias' => 'index',
            )
        )
    ),
    // List
    'list'     => array
    (
        'sorting'           => array
        (
            'mode' => 6
            // TODO: Sorting by language is not possible in mode 6?
        ),
        'label'             => array
        (
            'fields'         => array('title', 'language'),
            'label_callback' => array('tl_page_i18nl10n', 'labelCallback')
        ),
        // Global operations
        'global_operations' => array
        (
            'toggleNodes' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['toggleNodes'],
                'href'  => 'ptg=all',
                'class' => 'header_toggle'
            ),
            'all'         => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
        ),
        // Item operations
        'operations'        => array
        (
            'edit'        => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
                'button_callback' => array('tl_page_i18nl10n', 'createButton')
            ),
            'copy'        => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
                'button_callback' => array('tl_page_i18nl10n', 'createButton')
            ),
            'delete'      => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
                'button_callback' => array('tl_page_i18nl10n', 'createButton')
            ),
            'toggle_l10n' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return I18nl10n.toggleL10n(this,%s,\'tl_page_i18nl10n\')"',
                'button_callback' => array('tl_page_i18nl10n', 'toggleIcon')
            ),
            'show'        => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        )
    ),
    // Palettes
    'palettes' => array
    (
        '__selector__' => array('type'),
        'default'      => '{i18nl10n_menuLegend},title,alias;'
                          . '{i18nl10n_metaLegend},pageTitle,description;'
                          . '{i18nl10n_timeLegend:hide},dateFormat,timeFormat,datimFormat;'
                          . '{i18nl10n_expertLegend:hide},cssClass;'
                          . '{publish_legend},start,stop;'
                          . '{i18nl10n_legend},language,i18nl10n_published',
        'redirect'     => '{i18nl10n_menuLegend},title,alias;'
                          . '{i18nl10n_metaLegend},pageTitle;'
                          . '{redirect_legend},url;'
                          . '{i18nl10n_expertLegend:hide},cssClass;'
                          . '{publish_legend},start,stop;'
                          . '{i18nl10n_legend},language,i18nl10n_published'
    ),
    // Fields
    'fields'   => array
    (
        'id'             => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'            => array
        (
            'foreignKey' => 'tl_page.id',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => array(
                'type' => 'belongsTo',
                'load' => 'eager'
            )
        ),
        'language'      => array(
            'label'            => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'search'           => true,
            'options_callback' => array('tl_page_i18nl10n', 'languageOptions'),
            'reference'        => &$GLOBALS['TL_LANG']['LNG'],
            'eval'             => array(
                'mandatory'          => true,
                'rgxp'               => 'language',
                'maxlength'          => 5,
                'nospace'            => true,
                'doNotCopy'          => true,
                'tl_class'           => 'w50 clr',
                'includeBlankOption' => true
            ),
            'sql'                     => "varchar(5) NOT NULL default ''"
        ),
        // copy settings from tl_page dca
        'sorting'            => $GLOBALS['TL_DCA']['tl_page']['fields']['sorting'],
        'tstamp'             => $GLOBALS['TL_DCA']['tl_page']['fields']['tstamp'],
        'title'              => $GLOBALS['TL_DCA']['tl_page']['fields']['title'],
        'alias'              => $GLOBALS['TL_DCA']['tl_page']['fields']['alias'],
        'type'               => $GLOBALS['TL_DCA']['tl_page']['fields']['type'],
        'pageTitle'          => $GLOBALS['TL_DCA']['tl_page']['fields']['pageTitle'],
        'description'        => $GLOBALS['TL_DCA']['tl_page']['fields']['description'],
        'url'                => $GLOBALS['TL_DCA']['tl_page']['fields']['url'],
        'cssClass'           => $GLOBALS['TL_DCA']['tl_page']['fields']['cssClass'],
        'dateFormat'         => $GLOBALS['TL_DCA']['tl_page']['fields']['dateFormat'],
        'timeFormat'         => $GLOBALS['TL_DCA']['tl_page']['fields']['timeFormat'],
        'datimFormat'        => $GLOBALS['TL_DCA']['tl_page']['fields']['datimFormat'],
        'start'              => $GLOBALS['TL_DCA']['tl_page']['fields']['start'],
        'stop'               => $GLOBALS['TL_DCA']['tl_page']['fields']['stop'],
        'i18nl10n_published' => $GLOBALS['TL_DCA']['tl_page']['fields']['i18nl10n_published']
    )
);

// update fields
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['title']['eval']['tl_class']          = 'w50';
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['alias']['save_callback']             = array
(
    array('tl_page_i18nl10n', 'generateAlias')
);
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['url']['eval']['mandatory']           = false;
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['url']['eval']['tl_class']            = 'long';
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['i18nl10n_published']['eval']['tl_class'] = 'w50 m12';

// Splice in localize all in case languages are available
if ($enableCreate) {
    $additionalFunctions = array(
        'localize_all' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all'],
            'href'       => 'localize_all=1',
            'class'      => 'header_l10n_localize_all',
            'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
        )
    );

    array_splice(
        $GLOBALS['TL_DCA']['tl_page_i18nl10n']['list']['global_operations'],
        0,
        0,
        $additionalFunctions
    );
};


/**
 * Class tl_page_i18nl10n
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_page_i18nl10n extends tl_page
{

    /**
     * Generate a localization icon for treeview
     *
     * @param               $row
     * @param               $label
     * @param DataContainer $dc
     * @param string        $imageAttribute
     * @param bool          $blnReturnImage
     * @param bool          $blnProtected
     *
     * @return string
     */
    public function labelCallback($row, $label, DataContainer $dc = null, $imageAttribute = '', $blnReturnImage = false, $blnProtected = false)
    {
        $src = 'system/modules/i18nl10n/assets/img/flag_icons/' . $row['language'];

        $src .= $row['i18nl10n_published'] ? '.png' : '_invisible.png';

        $label = '<span class="i18nl10n_page"><img class="i18nl10n_flag" src="%1$s"> %2$s [%3$s]</span>';

        return sprintf(
            $label,
            $src,
            specialchars($row['title']),
            $GLOBALS['TL_LANG']['LNG'][$row['language']]
        );
    }

    /**
     * Handle 'localize_all' request on onload_callback
     *
     * @return void
     */
    public function localizeAllHandler()
    {
        // Decide if message is shown or action is executed
        if (\Input::get('localize_all') && !\Input::post('localize_all')) {
            self::localizeAllMessage();
        } elseif (\Input::post('localize_all_')) {
            self::localizeAllAction();
        }
    }

    /**
     * Show localize all message and form
     *
     * @return void
     */
    private function localizeAllMessage()
    {
        $arrLanguages       = I18nl10n::getAllLanguages();
        $strFlagPath        = 'system/modules/i18nl10n/assets/img/flag_icons/';
        $strMessage         = $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'];
        $strDomainLanguages = '';

        foreach ($arrLanguages as $key => $domain) {
            $strDomainLocalization = '';

            if (count($domain['localizations'])) {
                foreach ($domain['localizations'] as $localization) {
                    $strDomainLocalization .= sprintf(
                        '<li><img class="i18nl10n_flag" src="%1$s.png" /> %2$s</li>',
                        $strFlagPath . $localization,
                        $GLOBALS['TL_LANG']['LNG'][$localization]
                    );
                }
            } else {
                $strDomainLocalization .= sprintf(
                    '<li>%s</li>',
                    $GLOBALS['TL_LANG']['tl_page_i18nl10n']['no_languages']
                );
            }

            $strDomainLanguages .= sprintf(
                '<li class="i18nl10n_localize_domain"><img class="i18nl10n_flag" src="%1$s.png" /> %2$s<ul>%3$s</ul></li>',
                $strFlagPath . $domain['default'],
                $key ?: '*',
                $strDomainLocalization
            );
        }

        $strDomainList = sprintf(
            '<ul class="i18nl10n_localize_list">%s</ul>',
            $strDomainLanguages
        );

        $html =
            '<form class="i18nl10n_localize" method="post" action="contao/main.php?do=%1$s">
                <div class="i18nl10n_message">
                    %2$s %3$s
                    <div class="tl_submit_container">
                        <a href="contao/main.php?do=%1$s">%4$s</a>
                        <input type="submit" value="%5$s" class="tl_submit" name="localize_all_" />
                    </div>
                </div>
                <input type="hidden" name="REQUEST_TOKEN" value="%6$s">
            </form>';

        $rawMessage = sprintf(
            $html,
            \Input::get('do'),
            $strMessage,
            $strDomainList,
            utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['no']),
            utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['yes']),
            REQUEST_TOKEN
        );

        \Message::addRaw($rawMessage);

    }


    /**
     * Localize all pages with missing localization
     *
     * @return void
     */
    private function localizeAllAction()
    {
        $arrLanguages = I18nl10n::getAllLanguages();

        foreach ($arrLanguages as $domain) {
            $arrPageIds = $this->Database->getChildRecords(array($domain['rootId']), 'tl_page');

            // Add root page to pageIds
            array_push($arrPageIds, $domain['rootId']);

            foreach ($domain['localizations'] as $localization) {

                $strPageIds = implode($arrPageIds, ',');

                $sql = "
                  INSERT INTO
                    tl_page_i18nl10n
                    (
                        pid, sorting, tstamp, language, title, type, pageTitle, description, cssClass,
                        alias, i18nl10n_published, start, stop, dateFormat, timeFormat, datimFormat
                    )
                  SELECT
                    p.id AS pid, p.sorting, p.tstamp, ? AS language,p.title, p.type, p.pageTitle,
                    p.description, p.cssClass, p.alias, p.published, p.start, p.stop,
                    p.dateFormat, p.timeFormat, p.datimFormat
                  FROM
                    tl_page as p
                  LEFT JOIN
                    tl_page_i18nl10n as i
                      ON p.id = i.pid
                      AND i.language = ?
                  WHERE
                    p.id IN($strPageIds)
                    AND (p.language = ? OR p.language = '')
                    AND i.pid IS NULL
                ";

                \Database::getInstance()
                    ->prepare($sql)
                    ->execute($localization, $localization, $domain['default']);
            }
        }
    }


    /**
     * Display message when no languages are set up
     *
     * @return  void
     */
    public function displayLanguageMessage()
    {
        $arrLanguages = I18nl10n::getAllLanguages();
        $info         = false;
        $error        = false;

        // If no root pages found, give error
        if (!count($arrLanguages)) {
            $error = sprintf(
                $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_root'],
                $GLOBALS['TL_LANG']['MOD']['page'][0]
            );
        } else {
            $countLanguages = 0;

            // Loop roots
            foreach ($arrLanguages as $root) {
                $countLanguages += count($root['localizations']);

                // If a root page has no alternative languages, give info
                if (!count($root['localizations'])) {
                    $info = &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_some_languages'];
                }
            }

            // If no languages found, give error
            if (!$countLanguages) {
                $error = sprintf(
                    $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'],
                    $GLOBALS['TL_LANG']['MOD']['page'][0]
                );
            }
        }

        // Show error or info if needed
        if ($error) {
            \Message::addError($error);
        } elseif ($info) {
            \Message::addInfo($info);
        }
    }


    /**
     * Easily publish/unpublish a page localization
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc = null)
    {
        // Check permissions to edit
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');
        $this->checkPermission();

        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page::published', 'alexf')) {
            $this->log(
                'Not enough permissions to publish/unpublish L10N page ID "' . $intId . '"',
                'tl_page_i18nl10n toggleVisibility',
                TL_ERROR
            );
            $this->redirect('contao/main.php?act=error');
        }

        $objVersions = new \Versions('tl_page_i18nl10n', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['i18nl10n_published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['i18nl10n_published']['save_callback'] as $callback) {
                $this->import($callback[0]);
                $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
            }
        }

        $sql = "
            UPDATE tl_page_i18nl10n
            SET tstamp = " . time() . ", i18nl10n_published='" . ($blnVisible ? 1 : '') . "'
            WHERE id = ?
        ";

        // Update the database
        \Database::getInstance()
            ->prepare($sql)
            ->execute($intId);

        $objVersions->initialize('tl_page_i18nl10n', $intId);
    }


    /**
     * Return 'toggle visibility' button on view create
     *
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(\Input::get('tid'))) {
            $this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page_i18nl10n::i18nl10n_published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

        if (!$row['i18nl10n_published']) {
            $icon = 'invisible.gif';
        }

        $sql = "
            SELECT *
            FROM tl_page
            WHERE
              id = (
                SELECT pid
                FROM tl_page_i18nl10n
                WHERE id = ?
              )
        ";

        $objPage = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($row['id']);

        // only return image element for icon
        if (!$this->User->isAdmin && !$this->User->isAllowed(2, $objPage->row())) {
            return \Image::getHtml($icon);
        }

        // return linked image element for icon
        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>'
               . \Image::getHtml($icon, $label) . '</a> ';
    }

    /**
     * Display invalid config error message
     */
    public function displayAddLanguageToUrlMessage()
    {
        if (\Config::get('addLanguageToUrl')) {
            $message = $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_add_language_to_url'];
            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
                '<div class="i18nl10n_message">' . $message . '</div>';
        };
    }

    /**
     * Toggle eye icon
     *
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @param $attributes
     *
     * @return string
     */
    public function toggleL10nIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $this->import('BackendUser', 'User');

        if (strlen(\Input::get('tid'))) {
            self::toggleL10n(\Input::get('tid'), (\Input::get('state') == 0), 'tl_page');
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page::i18nl10n_published', 'alexf')) {
            return '';
        }

        $href .= '&amp;id=' . \Input::get('id') . '&amp;tid=' . $row['id'] . '&amp;state=' . $row[''];

        if (!$row['i18nl10n_published']) {
            $icon = 'invisible.gif';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>'
               . \Image::getHtml($icon, $label) . '</a> ';
    }

    /**
     * Toggle localized page visibility
     *
     * @param $intId
     * @param $blnPublished
     */
    public function toggleL10n($intId, $blnPublished)
    {
        $strTable = 'tl_page_i18nl10n';

        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess($strTable . '::i18nl10n_published', 'alexf')) {
            $this->log(
                'Not enough permissions to show/hide record ID "' . $intId . '"',
                $strTable . ' toggleVisibility',
                TL_ERROR
            );
            $this->redirect('contao/main.php?act=error');
        }

        // prepare versions
        $objVersions = new \Versions($strTable, $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA'][$strTable]['fields']['i18nl10n_published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA'][$strTable]['fields']['i18nl10n_published']['save_callback'] as $callback) {
                $this->import($callback[0]);
                $blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
            }
        }

        $sql = "UPDATE " . $strTable . " SET tstamp = ?, i18nl10n_published = ? WHERE id = ?";

        // Update the database
        \Database::getInstance()
            ->prepare($sql)
            ->execute(
                time(),
                $blnPublished ? '' : '1',
                $intId
            );

        // create new version
        $objVersions->create();
    }


    /**
     * Execute ajax requests
     *
     * @param $strAction
     *
     * @return bool
     */
    public function executePostActions($strAction)
    {
        switch ($strAction) {
            case 'toggleL10n':
                tl_page_i18nl10n::toggleL10n(
                    \Input::post('id'),
                    \Input::post('state') == 1
                );
                break;
        }
    }

    /**
     * Auto-generate a page alias if it has not been set yet
     *
     * @param mixed
     * @param \DataContainer
     *
     * @return string
     * @throws \Exception
     */
    public function generateAlias($varValue, DataContainer $dc)
    {
        $autoAlias   = false;
        $strLanguage = $dc->activeRecord->language;

        // Generate an alias if there is none
        if ($varValue === '') {
            $autoAlias = true;
            $varValue  = standardize(String::restoreBasicEntities($dc->activeRecord->title));

            // Generate folder URL aliases (see #4933)
            if (Config::get('folderUrl')) {
                // Get parent page
                $objBaseLangPage = \PageModel::findWithDetails($dc->activeRecord->pid);

                // Get translation for parent page
                $objL10nParentPage = I18nl10n::findL10nWithDetails($objBaseLangPage->pid, $strLanguage);

                // Only create folder url if parent is not root
                if ($objL10nParentPage && $objL10nParentPage->type !== 'root') {
                    // Create folder url
                    $varValue = $objL10nParentPage->alias . '/' . $varValue;
                }
            }
        }

        $objAlias = $this->Database
            ->prepare("SELECT pid FROM tl_page_i18nl10n WHERE (id = ? OR alias = ?) AND language = ?")
            ->execute($dc->id, $varValue, $strLanguage);

        // Check whether the page alias exists
        if ($objAlias->numRows > ($autoAlias ? 0 : 1)) {
            $arrPages = array();
            $strDomain = '';

            // Build domain array based on pages
            while ($objAlias->next()) {
                $objCurrentPage = \PageModel::findWithDetails($objAlias->pid);
                $domain = $objCurrentPage->domain ?: '*';

                // Store the current page's data
                if ($objCurrentPage->id === $dc->activeRecord->pid) {
                    $strDomain = ($objCurrentPage->type === 'root')
                        ? \Input::post('dns')
                        : $domain;
                } else {
                    $arrPages[$domain][] = $objAlias->pid;
                }
            }

            // Check if there are multiple results for the current domain
            if (!empty($arrPages[$strDomain])) {
                if ($autoAlias) {
                    $varValue .= '-' . $dc->id;
                } else {
                    throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
                }
            }
        }

        return $varValue;
    }

    /**
     * Create language options based on root page, already used languages and user permissions
     *
     * @param DataContainer $dc
     *
     * @return array
     */
    public function languageOptions(DataContainer $dc)
    {
        // Create identifier string for permission test
        $objFallbackPage = \PageModel::findWithDetails($dc->activeRecord->pid);
        $rootId          = $objFallbackPage->rootId;
        $strIdentifier   = $rootId . '::';

        // Set variables
        $arrLanguages    = $GLOBALS['TL_LANG']['LNG'];
        $arrOptions      = array();
        $id              = $dc->activeRecord->id;

        $i18nl10nLanguages = I18nl10n::getLanguagesByPageId($id, 'tl_page_i18nl10n', false);

        // Get already used languages
        $arrSiblingLanguages = $this->Database
            ->prepare('SELECT GROUP_CONCAT(language) as language FROM tl_page_i18nl10n WHERE pid = ? && id != ?')
            ->execute($dc->activeRecord->pid, $id)
            ->fetchAssoc();

        $arrSiblingLanguages = explode(',', $arrSiblingLanguages['language']);

        // Create options array base on root page languages and user permission
        foreach ($i18nl10nLanguages['localizations'] as $language) {
            if (!in_array($language, $arrSiblingLanguages)
                && ($this->User->isAdmin || in_array($strIdentifier . $language, (array) $this->User->i18nl10n_languages))) {
                $arrOptions[$language] = $arrLanguages[$language];
            }
        }

        return $arrOptions;
    }

    /**
     * Check permissions to edit table tl_page_i18nl10n
     */
    public function checkPermission()
    {
        if ($this->User->isAdmin) {
            return;
        }

        // Restrict the page tree
        $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['root'] = $this->User->pagemounts;
    }

    public function createButton($row, $href, $label, $title, $icon, $attributes) {

        $strLanguageIdentifier = $this->getLanguageIdentifierByLocalizationRow($row);

        return ($this->User->isAdmin || in_array($strLanguageIdentifier, (array) $this->User->i18nl10n_languages))
            ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> '
            : '';
    }

    /**
     * Create language identifier
     *
     * Get an identifier string of combined root page id and language
     * based on localization page row
     *
     * @param $arrRow
     *
     * @return string
     */
    private function getLanguageIdentifierByLocalizationRow($arrRow)
    {
        $objPage = \PageModel::findWithDetails($arrRow['pid']);

        return $objPage->rootId . '::' . $arrRow['language'];
    }
}
