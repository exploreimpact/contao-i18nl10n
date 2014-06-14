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

$GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][] = array
(
    'tl_page_l10n',
    'generatePageL10n'
);

foreach($GLOBALS['TL_DCA']['tl_page']['palettes'] as $k => $v){
    $GLOBALS['TL_DCA']['tl_page']['palettes'][$k] = str_replace('published,', 'published,i18nl10n_published,', $v);
}

$GLOBALS['TL_DCA']['tl_page']['fields']['published']['eval']['tl_class'] = 'w50';
$GLOBALS['TL_DCA']['tl_page']['fields']['i18nl10n_published'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['i18nl10n_published'],
    'default'   => true,
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'doNotCopy'=>true,
        'tl_class'=>'w50'
    ),
    'sql' => "char(1) NOT NULL default '1'"
);


class tl_page_l10n extends \Backend {
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