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

/**
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_news']['list']['operations']['news_i18nl10n'] = array
(
    'label'               => 'L10N',
    'href'                => 'do=news_i18nl10n',
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


class tl_news_l10n extends tl_news
{

    public function editL10n($row, $href, $label, $title, $icon)
    {
        $title = sprintf($GLOBALS['TL_LANG']['MSC']['editL10n'],"\"{$row['title']}\"");
        $buttonURL = $this->addToUrl($href . '&amp;id=' . $row['id'] . '&amp;table=tl_news_i18nl10n') ;

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

        $strRow = '
            <div class="tl_content_left">'
                . $arrRow['headline'] . '
                <span style="color:#b3b3b3;padding-left:3px">[' . Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['date']) . ']</span>
                <div class="i18nl10n_languages">
                    <span class="i18nl10n_language_item">
                        <a href="contao/main.php?do=news&table=tl_news&key=editTranslation&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                            <img src="system/modules/core_i18nl10n/assets/img/flag_icons/png/de.png" title="" class="i18nl10n_flag">
                        </a>
                        <span class="i18nl10n_language_functions">
                            <a href="contao/main.php?do=news&table=tl_news_i18nl10n&key=i18nl10n_toggle&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                                <img src="system/themes/default/images/visible.gif" title="Hide/unhide translation" class="i18nl10n_language_show">
                            </a>
                            <a href="contao/main.php?do=news&table=tl_news_i18nl10n&key=i18nl10n_delete&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                                <img src="system/themes/default/images/delete.gif" title="Remove translation" class="i18nl10n_language_delete">
                            </a>
                        |
                        </span>
                    </span>
                    <span class="i18nl10n_language_item">
                        <a href="contao/main.php?do=news&table=tl_news_i18nl10n&key=i18nl10n_edit&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                            <img src="system/modules/core_i18nl10n/assets/img/flag_icons/png/en.png" title="" class="i18nl10n_flag">
                        </a>
                        <span class="i18nl10n_language_functions">
                            <a href="contao/main.php?do=news&table=tl_news_i18nl10n&key=i18nl10n_toggle&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                                <img src="system/themes/default/images/visible.gif" title="Hide/unhide translation" class="i18nl10n_language_show">
                            </a>
                            <a href="contao/main.php?do=news&table=tl_news_i18nl10n&key=i18nl10n_delete&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                                <img src="system/themes/default/images/delete.gif" title="Remove translation" class="i18nl10n_language_delete">
                            </a>
                        </span>
                    </span>
                    |
                    <a href="contao/main.php?do=news&table=tl_news&key=i18nl10n_create&id=1&rt=3b715d9536ddc6161d7ed29de27aa193&ref=19d11ab6">
                        <img src="../system/themes/default/images/new.gif">
                    </a>
                </div>
            </div>
        ';

        return $strRow;
    }

    public function checkPermission() {
        \FB::log(\Input::get('key'));
    }

    public function create() {
        $this->redirect('contao/main.php?do=news&table=tl_news&id=1&act=create&mode=2&pid=1&rt=' . REQUEST_TOKEN . '&ref=' . \Input::get('ref'));
    }

    public function createNewsTranslation() {

    }

    public function editTranslation() {

    }

    public function deleteTranslation() {

    }

    public function toggleTranslation() {

    }

}