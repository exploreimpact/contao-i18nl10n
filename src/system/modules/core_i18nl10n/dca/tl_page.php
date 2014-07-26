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

use Verstaerker\I18nl10n\Classes\I18nl10n as I18nl10n;

// load language translations
$this->loadLanguageFile('languages');

// load page dca
//$this->loadDataContainer('tl_page');


/**
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['page_i18nl10n'] = array
(
    'label'               => 'L10N',
    'href'                => 'do=core_i18nl10n',
    'button_callback'     => array('tl_page_l10n', 'editL10n')
);

$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = array
(
    'tl_page_l10n',
    'setDefaultLanguage'
);

$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = array
(
    'tl_page_l10n',
    'localizeAll'
);

$GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][] = array
(
    'tl_page_l10n',
    'generatePageL10n'
);

if (Input::get('do') == 'page') {
    $GLOBALS['TL_DCA']['tl_page']['list']['label']['label_callback'] = array
    (
        'tl_page_l10n',
        'listPagesL10n'
    );
}

foreach($GLOBALS['TL_DCA']['tl_page']['palettes'] as $k => $v){
    $GLOBALS['TL_DCA']['tl_page']['palettes'][$k] = str_replace('published,', 'published,l10n_published,', $v);
}

$GLOBALS['TL_DCA']['tl_page']['fields']['published']['eval']['tl_class'] = 'w50';
$GLOBALS['TL_DCA']['tl_page']['fields']['l10n_published'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['l10n_published'],
    'default'   => true,
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'doNotCopy'=>true,
        'tl_class'=>'w50'
    ),
    'sql' => "char(1) NOT NULL default '1'"
);


/**
 * Create array of non default i18nl10n languages
 */
$i18nl10n_languages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);

// remove default language
foreach($i18nl10n_languages as $k => $v) {
    if($v == $GLOBALS['TL_CONFIG']['i18nl10n_default_language']) {
        $i18nl10n_languages = array_delete($i18nl10n_languages,$k);
        break;
    }
}

$GLOBALS['i18nl10n_languages'] = $i18nl10n_languages;


/**
 * Define additional global operations
 */
if(is_array($i18nl10n_languages) && count($i18nl10n_languages) > 1) // if alternative languages are set
{
    $i18nl10nOperations = array(
        'toggleL10n' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_page']['toggleL10n'],
            'class'      => 'header_l10n_toggle',
            'attributes' => 'onclick="Backend.getScrollOffset();I18nl10n.toggleFunctions();return false;"'
        ),
        'localize_all' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_page']['localize_all'],
            'href'       => 'localize_all=1',
            'class'      => 'header_l10n_localize_all',
            'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
        )
    );
} else { // if no alternative langauges are set
    $i18nl10nOperations = array(
        'define_language' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_page']['define_language'],
            'href'       => 'do=settings',
            'class'      => 'header_l10n_define_language',
        )
    );
};

array_splice(
    $GLOBALS['TL_DCA']['tl_page']['list']['global_operations'],
    count($GLOBALS['TL_DCA']['tl_page']['list']['global_operations']) - 1,
    0,
    $i18nl10nOperations
);


class tl_page_l10n extends tl_page {
    public function editL10n($row, $href, $label, $title, $icon)
    {
        //TODO: think about a new page type: regular_localized
        $title = sprintf($GLOBALS['TL_LANG']['MSC']['editL10n'],"\"{$row['title']}\"");
        $buttonURL = $this->addToUrl($href.'&amp;node='.$row['id']);
        $button = '<a href="' . $buttonURL . '" title="' . specialchars($title) . '"><img src="system/modules/core_i18nl10n/assets/img/i18nl10n.png" /></a>';

        return $button;
    }


    /**
     * Apply the root page language to new pages
     */
    public function setDefaultLanguage()
    {
        if ($this->Input->get('act') != 'create')
        {
            return;
        }

        if ($this->Input->get('pid') == 0)
        {
            $GLOBALS['TL_DCA']['tl_page']['fields']['language']['default'] = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
        }
        else
        {
            $objPage = \PageModel::findWithDetails($this->Input->get('pid'));
            $GLOBALS['TL_DCA']['tl_page']['fields']['language']['default'] = $objPage->rootLanguage;
        }
    }


    public function listPagesL10n($arrRow, $strLabel, DataContainer $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false) {

        $strLabel = parent::addIcon($arrRow, $strLabel, $dc, $imageAttribute, $blnReturnImage, $blnProtected);

        $strPageI18nl10nParam = 'do=core_i18nl10n&table=tl_page_i18nl10n';
        $strDefaultLang = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
        $strL10nItems = '';

        // define main values
        $templateValues = array
        (
            'pageToggleIcon' => self::toggleL10nIcon(
                    $arrRow,
                    null,
                    $strDefaultLang,
                    sprintf(
                        $GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['publish'],
                        $strDefaultLang
                    ),
                    'system/themes/default/images/visible.gif',
                    ' class="toggle_i10n" onclick="Backend.getScrollOffset();return I18nl10n.toggleL10n(this,\''.$arrRow['id'].'\',\'tl_page\')"'
                ),
//            'pageDate' => Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['date']),
//            'newsHeadline' => $arrRow['headline'],
            'pageEditTitle' =>  sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['edit'], $strDefaultLang),
//            'pageEditIcon' =>  $arrRow['l10n_published'] ? $strDefaultLang : $strDefaultLang . '_invisible',
            'pageEditUrl' => $this->addToUrl('do=page&table=tl_page&act=edit&id=' . $arrRow['id']),
            'newsLanguage' => $arrRow['l10n_published'] ? $strDefaultLang : $strDefaultLang . '_invisible',
            'createL10nUrl' => $this->addToUrl($strPageI18nl10nParam . '&pid=' . $arrRow['id'] . '&act=create&mode=2'),
            'createL10nAlt' => $GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['create'],
            'l10nToolsClass' => $GLOBALS['TL_CONFIG']['i18nl10n_alwaysShowL10n'] ? 'i18nl10n_languages open' : 'i18nl10n_languages'
        );

        // get related translations
        $arrL10n = self::getRelatedL10n($arrRow['id']);

        foreach($arrL10n as $l10n) {

            // define item values
            $templateValues['l10nItem' . $l10n['id'] . 'EditUrl'] = $this->addToUrl($strPageI18nl10nParam . '&id=' . $l10n['id'] . '&act=edit');
            $templateValues['l10nItem' . $l10n['id'] . 'EditTitle'] = sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['edit'], $l10n['language']);
            $templateValues['l10nItem' . $l10n['id'] . 'EditIcon'] = $l10n['l10n_published'] ? $l10n['language'] : $l10n['language'] . '_invisible';
            $templateValues['l10nItem' . $l10n['id'] . 'DeleteUrl'] = $this->addToUrl($strPageI18nl10nParam . '&id=' . $l10n['id'] . '&act=delete');
            $templateValues['l10nItem' . $l10n['id'] . 'Language'] = $l10n['language'];
            $templateValues['l10nItem' . $l10n['id'] . 'PublishTitle'] = sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['publish'], $l10n['language']);
            $templateValues['l10nItem' . $l10n['id'] . 'DeleteTitle'] = sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['delete'], $l10n['language']);
            $templateValues['l10nItem' . $l10n['id'] . 'DeleteConfirm'] = sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['deleteConfirm'], $GLOBALS['TL_LANG']['LNG'][$l10n['language']]);
            $templateValues['l10nItem' . $l10n['id'] . 'ToggleIcon'] = self::toggleL10nIcon(
                $l10n,
                null,
                $l10n['language'],
                sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['publish'], $l10n['language']),
                'system/themes/default/images/visible.gif',
                ' class="toggle_i10n" onclick="Backend.getScrollOffset();return I18nl10n.toggleL10n(this,\''.$l10n['id'].'\',\'tl_page_i18nl10n\')"'
            );

            // create template
            $strL10nItems .= '
                <span class="i18nl10n_language_item">
                    <a href="%l10nItem'.$l10n['id'].'EditUrl$s" title="%l10nItem'.$l10n['id'].'EditTitle$s">
                        <img src="system/modules/core_i18nl10n/assets/img/flag_icons/%l10nItem'.$l10n['id'].'EditIcon$s.png" alt="%l10nItem'.$l10n['id'].'Language$s" class="i18nl10n_flag">
                    </a>
                    <span class="i18nl10n_language_functions">
                        %l10nItem'.$l10n['id'].'ToggleIcon$s
                        <a href="%l10nItem'.$l10n['id'].'DeleteUrl$s" title="%l10nItem'.$l10n['id'].'DeleteTitle$s" onclick="if(!confirm(\'%l10nItem'.$l10n['id'].'DeleteConfirm$s\'))return false;Backend.getScrollOffset()">
                            <img src="system/themes/default/images/delete.gif" alt="%l10nItem'.$l10n['id'].'Language$s" class="i18nl10n_language_delete">
                        </a>
                    </span>
                    |
                </span>
            ';
        }

        // create main template and combine with l10n items
        $strLabelL10n = '
            <div class="%l10nToolsClass$s">
                <span class="i18nl10n_language_item">
                    <a href="%pageEditUrl$s" title="%pageEditTitle$s">
                        <img src="system/modules/core_i18nl10n/assets/img/flag_icons/%newsLanguage$s.png"
                             alt="%newsLanguage$s"
                             class="i18nl10n_flag">
                    </a>
                    <span class="i18nl10n_language_functions">%pageToggleIcon$s</span>
                    ::
                </span>
                ' . $strL10nItems . '
                <a href="%createL10nUrl$s" alt="%createL10nAlt$s">
                    <img src="system/themes/default/images/new.gif">
                </a>
            </div>
        ';

        // insert l10n functions into article label
        $strLabel = preg_replace('@^(.*)(</div>|</a>)$@', I18nl10n::vnsprintf('$1' . $strLabelL10n . '$2', $templateValues), $strLabel);


        return $strLabel;
    }


    public function toggleL10nIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $this->import('BackendUser', 'User');

        if (strlen($this->Input->get('tid')))
        {
            self::toggleL10n($this->Input->get('tid'), ($this->Input->get('state') == 0), 'tl_page');
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page::l10n_published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

        if (!$row['l10n_published'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
    }


    public function toggleL10n($intId, $blnPublished, $strTable)
    {

        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess($strTable . '::l10n_published', 'alexf'))
        {
            $this->log('Not enough permissions to show/hide record ID "'.$intId.'"', $strTable . ' toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        // prepare versions
        $objVersions = new \Versions($strTable, $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA'][$strTable]['fields']['l10n_published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA'][$strTable]['fields']['l10n_published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
            }
        }

        // TODO: Inject protection!! (also on news)
        $sql = "
            UPDATE "
                . $strTable .
            " SET
                tstamp=". time() .",
                l10n_published='" . ($blnPublished ? '' : '1') .
            "' WHERE id=?";

        // Update the database
        $this->Database
            ->prepare($sql)
            ->execute($intId);

        // create new version
        $objVersions->create();
    }


    private function getRelatedL10n($pid) {
        $arrL10n = \Database::getInstance()
            ->prepare('SELECT * FROM tl_page_i18nl10n WHERE pid = ? ORDER BY language')
            ->execute($pid)
            ->fetchAllassoc();

        return $arrL10n;
    }


    /**
     * Create localization for all pages
     */
    public function localizeAll()
    {
        \Verstaerker\I18nl10n\Classes\I18nl10n::localizeAll();
    }


    public function executePostActions($strAction)
    {
        switch(\Input::post('table')) {
            case 'tl_page':
            case 'tl_page_i18nl10n':
                switch($strAction) {
                    case 'toggleL10n':
                        tl_page_l10n::toggleL10n(
                            \Input::post('id'),
                            \Input::post('state') == 1,
                            \Input::post('table')
                        );
                        break;
                }
                break;

            default:
                return false;
        }
    }


    /**
     * Automatically create a new localization
     * upon page creation - similar to tl_page::generateArticle()
     *
     */
    public function generatePageL10n(DataContainer $dc) {
        if(!$dc->activeRecord || $dc->activeRecord->tstamp > 0)
        {
            return;
        }

        $new_records = $this->Session->get('new_records');

        // Not a new page - copy/paste is a great way to share code :P
        if (!$new_records
            || (is_array($new_records[$dc->table])
                && !in_array($dc->id, $new_records[$dc->table])))
        {
            return;
        }

        //now make copies in each language.
        $site_langs = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);

        $fields = array (
            'pid'     => $dc->id,
            'sorting' => 0,
            'tstamp'  => time(),
            'title' => $dc->activeRecord->title,
            'alias' => $dc->activeRecord->alias,
            'type'  => $dc->activeRecord->type,
            'pageTitle' => $dc->activeRecord->pageTitle,
            'description' => $dc->activeRecord->description,
            'cssClass' => $dc->activeRecord->cssClass,
            'published' => $dc->activeRecord->published,
            'start' => $dc->activeRecord->start,
            'stop'  => $dc->activeRecord->stop,
            'dateFormat' => $dc->activeRecord->dateFormat,
            'timeFormat' => $dc->activeRecord->timeFormat,
            'datimFormat' => $dc->activeRecord->datimFormat
        );

        foreach ($site_langs as $lang) {
            if($lang == $GLOBALS['TL_CONFIG']['i18nl10n_default_language']) continue;

            $fields['sorting'] +=128;
            $fields['language'] = $lang;

            $sql = "
              INSERT INTO
                tl_page_i18nl10n %s
            ";

            \Database::getInstance()
                ->prepare($sql)
                ->set($fields)
                ->execute();
        }
    }
}