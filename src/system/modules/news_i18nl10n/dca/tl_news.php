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
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

use Verstaerker\I18nl10n\Classes\I18nl10n as I18nl10n;

/**
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_news']['list']['operations']['news_i18nl10n'] = array
(
    'label'               => 'L10N',
    'href'                => 'do=news_i18nl10n&table=tl_news_i18nl10n',
    'button_callback'     => array('tl_news_l10n', 'editL10n')
);

$GLOBALS['TL_DCA']['tl_news']['config']['onload_callback'] = array
(
    array('tl_news_l10n', 'checkPermission')
);

$GLOBALS['TL_DCA']['tl_news']['list']['sorting']['child_record_callback'] = array
(
    'tl_news_l10n',
    'listNewsArticlesL10n'
);

// adjust published css class
$GLOBALS['TL_DCA']['tl_news']['fields']['published']['eval']['tl_class'] = 'w50';

// insert i18nl10n_published
$GLOBALS['TL_DCA']['tl_news']['fields']['i18nl10n_published'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_news']['i18nl10n_published'],
    'default'   => true,
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'doNotCopy' => true,
        'tl_class'  => 'w50'
    ),
    'sql' => "char(1) NOT NULL default '1'"
);

// insert i18nl10n_publish into palette
$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = str_replace(
    ',published,',
    ',published,i18nl10n_published,',
    $GLOBALS['TL_DCA']['tl_news']['palettes']['default']
);

// add language toggle to operations
// TODO: remove this in final version
/*$GLOBALS['TL_DCA']['tl_news']['list']['operations']['toggle_i10n'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['tl_news']['toggle'],
    'icon'                => 'visible.gif',
    'attributes'          => 'onclick="Backend.getScrollOffset();return i18nl10n.toggleL10n(this,%s,\'tl_news\')"',
    'button_callback'     => array('tl_news_l10n', 'toggleL10nIcon')
);*/


class tl_news_l10n extends tl_news
{

    public function editL10n($row, $href, $label, $title, $icon)
    {
        $title = sprintf($GLOBALS['TL_LANG']['MSC']['editL10n'],"\"{$row['title']}\"");
        $buttonURL = $this->addToUrl($href . '&id=' . $row['id']) ;

        $button = '
            <a href="' . $buttonURL . '" title="' . specialchars($title) . '">
                <img src="system/modules/core_i18nl10n/assets/img/i18nl10n.png" />
            </a>';

        return $button;
    }


    /**
     * TODO: Give description
     * @param array
     * @return string
     */
    public function listNewsArticlesL10n($arrRow)
    {

        $strArticle = parent::listNewsArticles($arrRow);

        \FB::log($strArticle);

        $strNewsI18nl10nParam = 'do=news_i18nl10n&table=tl_news_i18nl10n&mode=2';
        $strDefaultLang = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
        $strL10nItems = '';

        $templateValues = array
        (
            'newsToggleIcon' => self::toggleL10nIcon(
                    $arrRow,
                    null,
                    $strDefaultLang,
                    sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['publish'], $strDefaultLang),
                    'system/themes/default/images/visible.gif',
                    ' class="toggle_i10n" onclick="Backend.getScrollOffset();return i18nl10n.toggleL10n(this,\''.$arrRow['id'].'\',\'tl_news\')"'
                ),
            'newsDate' => Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['date']),
            'newsHeadline' => $arrRow['headline'],
            'newsEditTitle' =>  sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['edit'], $strDefaultLang),
            'newsEditIcon' =>  $arrRow['i18nl10n_published'] ? $strDefaultLang : $strDefaultLang . '_invisible',
            'newsEditUrl' => $this->addToUrl('do=news&table=tl_news&act=edit&id=' . $arrRow['id']),
            'newsLanguage' => $strDefaultLang,
            'createL10nUrl' => $this->addToUrl($strNewsI18nl10nParam . '&pid=' . $arrRow['id'] . '&act=create'),
            'createL10nAlt' => $GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['create'],
            'l10nToolsClass' => $GLOBALS['TL_CONFIG']['i18nl10n_alwaysShowL10n'] ? 'i18nl10n_languages open' : 'i18nl10n_languages'
        );

        $arrL10n = $this->getRelatedL10n($arrRow['id']);

        foreach($arrL10n as $l10n) {

            $templateValues['l10nItem' . $l10n['id'] . 'EditUrl'] = $this->addToUrl($strNewsI18nl10nParam . '&id=' . $l10n['id'] . '&act=edit');
            $templateValues['l10nItem' . $l10n['id'] . 'EditTitle'] = sprintf($GLOBALS['TL_LANG']['MSC']['i18nl10n']['listNewsArticlesL10n']['edit'], $l10n['language']);
            $templateValues['l10nItem' . $l10n['id'] . 'EditIcon'] = $l10n['i18nl10n_published'] ? $l10n['language'] : $l10n['language'] . '_invisible';
            $templateValues['l10nItem' . $l10n['id'] . 'DeleteUrl'] = $this->addToUrl($strNewsI18nl10nParam . '&id=' . $l10n['id'] . '&act=delete');
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
                ' class="toggle_i10n" onclick="Backend.getScrollOffset();return i18nl10n.toggleL10n(this,\''.$l10n['id'].'\',\'tl_news_i18nl10n\')"'
            );

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

        $strArticleL10n = '
            <div class="%l10nToolsClass$s">
                <span class="i18nl10n_language_item">
                    <a href="%newsEditUrl$s" title="%newsEditTitle$s">
                        <img src="system/modules/core_i18nl10n/assets/img/flag_icons/%newsLanguage$s.png"
                             alt="%newsLanguage$s"
                             class="i18nl10n_flag">
                    </a>
                    <span class="i18nl10n_language_functions">%newsToggleIcon$s</span>
                    ::
                </span>
                ' . $strL10nItems . '
                <a href="%createL10nUrl$s" alt="%createL10nAlt$s">
                    <img src="system/themes/default/images/new.gif">
                </a>
            </div>
        ';

        // insert l10n functions into article label
        $strArticle = preg_replace('@(?=</div>$)@', I18nl10n::vnsprintf($strArticleL10n, $templateValues), $strArticle);

        return $strArticle;
    }

    public function checkPermission() {
//        \FB::log(\Input::get('key'));
    }

    public function create() {
        $this->redirect('contao/main.php?do=news&table=tl_news&id=1&act=create&mode=2&pid=1&rt=' . REQUEST_TOKEN . '&ref=' . \Input::get('ref'));
    }

    private function getRelatedL10n($pid) {
        $arrL10n = \Database::getInstance()
            ->prepare('SELECT * FROM tl_news_i18nl10n WHERE pid = ?')
            ->execute($pid)
            ->fetchAllassoc();

        return $arrL10n;
    }

    public function createNewsTranslation() {

    }

    public function editTranslation() {

    }

    public function deleteTranslation() {

    }

    public function toggleL10nIcon($row, $href, $label, $title, $icon, $attributes)
    {
//        \FB::log($label);
//        \FB::log($attributes);

        $this->import('BackendUser', 'User');

        if (strlen($this->Input->get('tid')))
        {
            $this->toggleL10n($this->Input->get('tid'), ($this->Input->get('state') == 0), 'tl_news');
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_news::i18nl10n_published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

        if (!$row['i18nl10n_published'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
    }

    public function toggleL10n($intId, $blnPublished, $strTable)
    {

        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess($strTable . '::i18nl10n_published', 'alexf'))
        {
            $this->log('Not enough permissions to show/hide record ID "'.$intId.'"', $strTable . ' toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        // prepare versions
        $objVersions = new \Versions('tl_news', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA'][$strTable]['fields']['i18nl10n_published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA'][$strTable]['fields']['i18nl10n_published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
            }
        }

        // Update the database
        $this->Database->prepare("UPDATE " . $strTable . " SET tstamp=". time() .", i18nl10n_published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
            ->execute($intId);

        // create new version
        $objVersions->create();
    }

    public function executePostActions($strAction)
    {
        switch(\Input::post('table')) {
            case 'tl_news':
            case 'tl_news_i18nl10n':
                switch($strAction) {
                    case 'toggleL10n':
                        tl_news_l10n::toggleL10n(\Input::post('id'), \Input::post('state') == 1, \Input::post('table'));
                        break;
                }
                break;

            default:
                return false;
        }
    }

}