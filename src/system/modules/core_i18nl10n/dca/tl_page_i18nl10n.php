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

// load language translations
$this->loadLanguageFile('languages');

// load tl_page class and translation
$this->loadLanguageFile('tl_page');
$this->loadDataContainer('tl_page');


/**
 * determine if languages are available to endable/disable editing
 */
$disableCreate = true;
$i18nl10n_languages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);

if(is_array($i18nl10n_languages) && count($i18nl10n_languages) > 1) {
    $disableCreate = false;
};


/**
 * Table tl_page_i18nl10n
 */
$GLOBALS['TL_DCA']['tl_page_i18nl10n'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_page',
        'enableVersioning'  => true,
        'closed'            => $disableCreate,
        'onload_callback'   => array
        (
            array('tl_page_i18nl10n', 'displayLanguageMessage'),
            array('tl_page_i18nl10n', 'localizeAll'),
            array('tl_page', 'addBreadcrumb'),
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid' => 'index',
                'language' => 'index',
                'alias' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'        => 6,
            'fields'      => array('language DESC')
        ),

        'label' => array
        (
            'fields'            => array('title', 'language'),
            'label_callback'    => array('tl_page_i18nl10n', 'addIcon')
        ),

        // Global operations
        'global_operations' => array
        (
            'define_language' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['define_language'],
                'href'       => 'do=settings',
                'class'      => 'header_define_language',
            ),

            'toggleNodes' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['toggleNodes'],
                'href'  => 'ptg=all',
                'class' => 'header_toggle'
            ),

            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
        ),

        // Item operations
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),

            'copy' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif'
            ),

            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),

            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => array('tl_page_i18nl10n', 'toggleIcon')
            ),

            'show' => array
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
        'default' =>
            '{menu_legend},title,alias;'
            . '{meta_legend},pageTitle,description;'
            . '{time_legend:hide},dateFormat,timeFormat,datimFormat;'
            . '{expert_legend:hide},cssClass;'
            . '{publish_legend},start,stop;'
            . '{i18nl10n_legend},language,i18nl10n_published'
    ),

    // Fields
    'fields' => array
    (
        /**
         * TODO: add alias localized support so for example alias
         * 'начало' links to 'home' with l10n enabled use
         * $GLOBALS['TL_HOOKS']['getPageIdFromUrl']
         */

        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),

        'pid' => array
        (
            'foreignKey' => 'tl_page.id',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => array(
                'type' => 'belongsTo',
                'load' => 'eager'
            )
        ),

        // copy settings from tl_page dca
        'sorting'            => $GLOBALS['TL_DCA']['tl_page']['fields']['sorting'],
        'tstamp'             => $GLOBALS['TL_DCA']['tl_page']['fields']['tstamp'],
        'type'               => $GLOBALS['TL_DCA']['tl_page']['fields']['type'],
        'title'              => $GLOBALS['TL_DCA']['tl_page']['fields']['title'],
        'language'           => $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
        'alias'              => $GLOBALS['TL_DCA']['tl_page']['fields']['alias'],
        'pageTitle'          => $GLOBALS['TL_DCA']['tl_page']['fields']['pageTitle'],
        'description'        => $GLOBALS['TL_DCA']['tl_page']['fields']['description'],
        'cssClass'           => $GLOBALS['TL_DCA']['tl_page']['fields']['cssClass'],
        'dateFormat'         => $GLOBALS['TL_DCA']['tl_page']['fields']['dateFormat'],
        'timeFormat'         => $GLOBALS['TL_DCA']['tl_page']['fields']['timeFormat'],
        'datimFormat'        => $GLOBALS['TL_DCA']['tl_page']['fields']['datimFormat'],
        'start'              => $GLOBALS['TL_DCA']['tl_page']['fields']['start'],
        'stop'               => $GLOBALS['TL_DCA']['tl_page']['fields']['stop'],
        'i18nl10n_published' => $GLOBALS['TL_DCA']['tl_page']['fields']['i18nl10n_published']
    )
);


/**
 * Splice in localize all in case languages are available
 */
if(is_array($i18nl10n_languages) && count($i18nl10n_languages) > 1) {
    $additionalFunctions = array(
        'localize_all' => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all'],
            'href'       => 'localize_all=1',
            'class'      => 'header_localize_all',
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
 * TODO: Merge this with above definition
 */
$i18nl10n_default_language = &$GLOBALS['TL_CONFIG']['i18nl10n_default_language'];

// remove default language
foreach($i18nl10n_languages as $k => $v) {
    if($v == $i18nl10n_default_language) {
        $i18nl10n_languages = array_delete($i18nl10n_languages,$k);
        break;
    }
}

$GLOBALS['i18nl10n_languages'] = $i18nl10n_languages;


/**
 * Merge in languages
 */
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['language'] = array_merge(
    $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
    array(
        'label'     => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
        'filter' => true,
        'inputType' => 'select',
        'options'   => $i18nl10n_languages,
        'reference'  => &$GLOBALS['TL_LANG']['LNG'],
        'eval' => array_merge(
                $GLOBALS['TL_DCA']['tl_page']['fields']['language']['eval'],
                array(
                'includeBlankOption' => true
            )
        )
    )
);



/**
 * Class tl_page_i18nl10n
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Krasimir Berov 2010-2011
 * @author     Krasimir Berov <http://i-can.eu>
 * @package    MultiLanguagePage
 * @license    LGPLv3
 */

class tl_page_i18nl10n extends tl_page
{
    /**
     * Generate a localization icon
     */
    public function addIcon($row, $label, DataContainer $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false)
    {
        $src ='system/modules/core_i18nl10n/assets/img/flag_icons/' . $row['language'] . '.png';

        $label = '<span style="color:#b3b3b3; padding-left:3px;">'
            . '<img style="vertical-align:middle" src="' . $src . '" /> '
            . specialchars($row['title']).' ['.$GLOBALS['TL_LANG']['LNG'][$row['language']].']'
            . '</span>';

        return $label;
    }


    /**
     * Localize all pages with a twist.
     */
    public function localizeAll()
    {
        if($this->Input->get('localize_all') && !$this->Input->post('localize_all')) {
            $flag = '<img class="i18nl10n_flag"'
                . ' src="system/modules/core_i18nl10n/assets/img/flag_icons/'
                . $GLOBALS['TL_CONFIG']['i18nl10n_default_language']
                . '.png" />&nbsp;';

            $message = sprintf($GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'], $flag . $GLOBALS['TL_LANG']['LNG'][$GLOBALS['TL_CONFIG']['i18nl10n_default_language']]);

            $newLanguages = '<ul class="i18nl10n_page_language_listing">';

            foreach(deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']) as $language) {
                if($language != $GLOBALS['TL_CONFIG']['i18nl10n_default_language']) {
                    $newLanguages .= '<li><img class="i18nl10n_flag" src="system/modules/core_i18nl10n/assets/img/flag_icons/' . $language . '.png" /> ' . $GLOBALS['TL_LANG']['LNG'][$language] . '</li>';
                }
            }

            $newLanguages .= '</ul>';

            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
                '<form method="post" action="contao/main.php?do=i18nl10n">'
                . '<div class="i18nl10n_page_message">' . $message . $newLanguages
                . '<div class="tl_submit_container">'
                . '<a href="contao/main.php?do=i18nl10n">'
                . utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['no'])
                . '</a>'
                . '<input type="submit" value="' . utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['yes']) . '" class="tl_submit" name="localize_all_" />'
                . '</div></div>'
                . '<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'"></form>';
        }
        //localise all pages
        elseif($this->Input->post('localize_all_')) {
            $defaultLanguage = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
            foreach($GLOBALS['i18nl10n_languages'] as $lang) {

                $sql = "
                  INSERT INTO
                    tl_page_i18nl10n
                    (
                        pid,sorting,tstamp,language,title,type,
                        pageTitle,description,cssClass,alias,
                        published,start,stop,dateFormat,timeFormat,datimFormat
                    )
                  SELECT
                    p.id AS pid, p.sorting, p.tstamp, '$lang' AS language,
                    p.title, p.type, p.pageTitle, p.description,
                    p.cssClass, p.alias, p.published, p.start, p.stop,
                    p.dateFormat, p.timeFormat, p.datimFormat
                  FROM
                    tl_page p
                  LEFT JOIN
                    tl_page_i18nl10n i
                      ON p.id = i.pid
                      AND i.language='$lang'
                  WHERE
                    (
                      p.language='$defaultLanguage'
                      OR p.language=''
                    )
                    AND p.type !='root'
                    AND i.pid IS NULL
                ";

                //TODO:use $objPage->rootLanguage
                \Database::getInstance()
                    ->prepare($sql)
                    ->execute();
            }
        }
    }


    /**
     * Display message when only basic language is available
     *
     * @return  void
     */
    public function displayLanguageMessage() {
        if(!is_array($GLOBALS['i18nl10n_languages'])
            || count($GLOBALS['i18nl10n_languages']) < 1) {

            // TODO: ref= would be nice for link
            $message = sprintf(
                $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'],
                'contao/main.php?do=settings'
            );

            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
                '<div class="i18nl10n_page_message">' . $message . '</div>';
        };
    }


    /**
     * Easily publish/unpublish a page localization
     *
     * @param integer
     * @param boolean
     */
    public function toggleVisibility($intId, $blnVisible)
    {
        // Check permissions to edit
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');
        $this->checkPermission();

        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page::published', 'alexf'))
        {
            $this->log('Not enough permissions to publish/unpublish L10N page ID "'.$intId.'"', 'tl_page_i18nl10n toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        $objVersions = new \Versions('tl_page_i18nl10n', $intId);
        $objVersions->initialize();

// Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
            }
        }

        $sql = "
            UPDATE
                tl_page_i18nl10n
            SET
                tstamp = " . time() . ", published='" . ($blnVisible ? 1 : '') . "'
            WHERE
                id=?
        ";

        // Update the database
        \Database::getInstance()
            ->prepare($sql)
            ->execute($intId);

        $objVersions->initialize('tl_page_i18nl10n', $intId);
    }


    /**
     * Return the "toggle visibility" button
     *
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen($this->Input->get('tid')))
        {
            $this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page::published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }

        $sql = "
            SELECT
              *
            FROM
              tl_page
            WHERE
              id =
              (
                SELECT
                  pid
                FROM
                  tl_page_i18nl10n
                WHERE
                  id = ?
              )
        ";

        $objPage = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($row['id']);

        // only return image element for icon
        if (!$this->User->isAdmin && !$this->User->isAllowed(2, $objPage->row()))
        {
            return \Image::getHtml($icon);
        }

        // return linked image element for icon
        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    }

    public function displayAddLanguageToUrlMessage() {
        if($GLOBALS['TL_CONFIG']['addLanguageToUrl']) {

            $message = $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_add_language_to_url'];

            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
                '<div class="i18nl10n_page_message">' . $message . '</div>';
        };
    }

}
