<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */


$this->loadLanguageFile('languages');

/**
 * Load class tl_page and its labels
 */
$this->loadLanguageFile('tl_page');
$this->loadDataContainer('tl_page');

$_tl_page_i18nl10n = &$GLOBALS['TL_LANG']['tl_page_i18nl10n'];
/**
 * Table tl_page_i18nl10n 
 */
$GLOBALS['TL_DCA']['tl_page_i18nl10n'] = array
(
    
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'ptable'                      => 'tl_page',
        'onload_callback' => array
        (
            array('tl_page_i18nl10n','displayAddLanguageToUrlMessage'),
            array('tl_page_i18nl10n','localize_all'),
            array('tl_page', 'addBreadcrumb'),
        ),
    
    ),

    // List
    'list' => array
    (
        'sorting' => array
    (
        'mode'                    => 6,
        'fields'                  => array('language DESC' ),
        'panelLayout'             => 'filter'
    ),
    'label' => array
    (
        'fields' => array('title', 'language','language'),
        //'format' => '%s <span style="color:#b3b3b3; padding-left:3px;'
        //        . '"><img style="vertical-align:middle"'
        //        .' src="system/modules/i18nl10n/html/flag_icons/png/%s.png" /> [%s]</span>',
        'label_callback' => array('tl_page_i18nl10n','addIcon')
    ),
    'global_operations' => array
    (
    'localize_all' => array
    (
        'label'               => &$_tl_page_i18nl10n['localize_all'],
        'href'                => 'localize_all=1',
        'class'               => 'header_localize_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
    ),
    'toggleNodes' => array
    (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['toggleNodes'],
        'href'                => '&amp;ptg=all',
        'class'               => 'header_toggle'
    ),
    'all' => array
    (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
    ),

    
    ),
    'operations' => array
    (
        'edit' => array
        (
            'label'               => &$_tl_page_i18nl10n['edit'],
            'href'                => 'act=edit',
            'icon'                => 'edit.gif'
        ),
        'copy' => array
        (
            'label'               => &$_tl_page_i18nl10n['copy'],
            'href'                => 'act=copy',
            'icon'                => 'copy.gif'
        ),
        'delete' => array
        (
            'label'               => &$_tl_page_i18nl10n['delete'],
            'href'                => 'act=delete',
            'icon'                => 'delete.gif',
            'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
        ),
        'toggle' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_page_i18nl10n']['toggle'],
        'icon'                => 'visible.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
        'button_callback'     => array('tl_page_i18nl10n', 'toggleIcon')
      ),
        'show' => array
        (
            'label'               => &$_tl_page_i18nl10n['show'],
            'href'                => 'act=show',
            'icon'                => 'show.gif'
        )
    )
    ),
    
/**
	// Subpalettes
	'subpalettes' => array
	(
		''                            => ''
	),
 */

);
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['palettes'] = array
    (
        'default' => '{menu_legend},title,alias,language;'
            .'{meta_legend},pageTitle,description;'
            .'{time_legend:hide},dateFormat,timeFormat,datimFormat;'
            .'{expert_legend:hide},cssClass;{publish_legend},published'
            .',start,stop'
            ,
    );
$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields'] = array
	(
	     'title'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['title'],
	     //add alias localized support so
	     //for example alias 'начало' links to 'home' with l10n enabled
	     //use $GLOBALS['TL_HOOKS']['getPageIdFromUrl']
	     'alias'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['alias'],
         'pageTitle'   => &$GLOBALS['TL_DCA']['tl_page']['fields']['pageTitle'],
         'description' => &$GLOBALS['TL_DCA']['tl_page']['fields']['description'],
         'cssClass'    => &$GLOBALS['TL_DCA']['tl_page']['fields']['cssClass'],
         'dateFormat'  => &$GLOBALS['TL_DCA']['tl_page']['fields']['dateFormat'],
         'timeFormat'  => &$GLOBALS['TL_DCA']['tl_page']['fields']['timeFormat'],
         'datimFormat' => &$GLOBALS['TL_DCA']['tl_page']['fields']['datimFormat'],
         'sitemapName' => &$GLOBALS['TL_DCA']['tl_page']['fields']['sitemapName'],
         'published'   => &$GLOBALS['TL_DCA']['tl_page']['fields']['published'],
         'start'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['start'],
         'stop'       => &$GLOBALS['TL_DCA']['tl_page']['fields']['stop']
         );

$i18nl10n_languages = deserialize($GLOBALS['TL_CONFIG']['i18nl10n_languages']);
$i18nl10n_default_language = &$GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
foreach($i18nl10n_languages as $k=>$v){
    if($v==$i18nl10n_default_language) {
        $i18nl10n_languages = array_delete($i18nl10n_languages,$k);
        break;
    }
}
$GLOBALS['i18nl10n_languages'] = $i18nl10n_languages;

$GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['language'] = array_merge(
        $GLOBALS['TL_DCA']['tl_page']['fields']['language'],
        array(
            'label'     => &$GLOBALS['TL_LANG']['MSC']['i18nl10n_fields']['language']['label'],
             'filter'   => true,
            'inputType' => 'select',
            'options'   => $i18nl10n_languages,
            'reference'  => &$GLOBALS['TL_LANG']['LNG'])
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
	 * @param array
	 * @param string
	 * @return string
	 */
	public function addIcon($row, $label, DataContainer $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false)
	{
	    //$image = $this->generateImage('iconPLAIN.gif', '', $folderAttribute);
	    $label ='<span style="color:#b3b3b3; padding-left:3px;'
                . '"><img style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/flag_icons/png/'
                .$row['language'].'.png" /> '.specialchars($row['title']).' ['.$GLOBALS['TL_LANG']['LNG'][$row['language']].']</span>';
	    return $label;
	}
	/**
	 * Localize all pages with a twist.
	 */
	public function localize_all() {
	    if($this->Input->get('localize_all')
	       && !$this->Input->post('localize_all')
	       ) {
            $flag = '<img style="vertical-align:middle"'
                .' src="system/modules/i18nl10n/html/flag_icons/png/'
                .$GLOBALS['TL_CONFIG']['i18nl10n_default_language']
                .'.png" /> ';
            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
           
            '<form method="post" action="contao/main.php?do=i18nl10n"
            ><div id="i18nl10n_localise_all_confirm">'
            .sprintf($GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all_q'],
                    $flag.$GLOBALS['TL_LANG']['LNG'][$GLOBALS['TL_CONFIG']['i18nl10n_default_language']]
                    )
            .'<div class="tl_submit_container"><input 
            type="submit" value="'
            .utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['yes']).'" 
            class="tl_submit" name="localize_all_" /> <a
            href="contao/main.php?do=i18nl10n">'
            .utf8_ucfirst($GLOBALS['TL_LANG']['MSC']['no']).'</a>&nbsp;
            </div></div><input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'"></form>'
            ;
        }
        //localise all pages 
        elseif($this->Input->post('localize_all_')){

                 
             foreach($GLOBALS['i18nl10n_languages'] as $l) {
            $SQL="
            INSERT INTO tl_page_i18nl10n (
pid,sorting,tstamp,language,title,type,
pageTitle,description,cssClass,alias,
published,start,stop,dateFormat,timeFormat,datimFormat)
SELECT p.id AS pid, p.sorting, p.tstamp, '$l' AS language, 
p.title, p.type, p.pageTitle, p.description,
p.cssClass, p.alias, p.published, p.start, p.stop,
p.dateFormat, p.timeFormat, p.datimFormat
FROM tl_page p LEFT JOIN tl_page_i18nl10n i 
ON p.id = i.pid AND i.language='$l' 
WHERE (p.language='"
.$GLOBALS['TL_CONFIG']['i18nl10n_default_language']
."' or p.language='')
AND p.type !='root' AND i.pid IS NULL";
                 //TODO:use $objPage->rootLanguage
                 //if a bug is reported 
                 //echo $SQL;
                 $this->Database->prepare($SQL)->execute();
             }
        }
    }
  /**
   * Easily publish/unpublish a page localization 
   * @param integer
   * @param boolean
   */
  public function toggleVisibility($intId, $blnVisible)
  {
    // Check permissions to edit
    $this->Input->setGet('id', $intId);
    $this->Input->setGet('act', 'toggle');
    $this->checkPermission();

    // Check permissions to publish
    if (!$this->User->isAdmin && !$this->User->hasAccess('tl_page::published', 'alexf'))
    {
      $this->log('Not enough permissions to publish/unpublish L10N page ID "'.$intId.'"', 'tl_page_i18nl10n toggleVisibility', TL_ERROR);
      $this->redirect('contao/main.php?act=error');
    }
    $this->createInitialVersion('tl_page_i18nl10n', $intId);

    // Trigger the save_callback
    if (is_array($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['published']['save_callback']))
    {
      foreach ($GLOBALS['TL_DCA']['tl_page_i18nl10n']['fields']['published']['save_callback'] as $callback)
      {
        $this->import($callback[0]);
        $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
      }
    }

    // Update the database
    $this->Database->prepare("UPDATE tl_page_i18nl10n SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
             ->execute($intId);

    $this->createNewVersion('tl_page_i18nl10n', $intId);
  }
  /**
   * Return the "toggle visibility" button
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

    $objPage = $this->Database->prepare("SELECT * FROM tl_page WHERE id=(SELECT pid FROM tl_page_i18nl10n WHERE id=?)")
                  ->limit(1)
                  ->execute($row['id']);

    if (!$this->User->isAdmin && !$this->User->isAllowed(2, $objPage->row()))
    {
      return $this->generateImage($icon) . ' ';
    }

    return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
  }

    public function displayAddLanguageToUrlMessage() {
        if($GLOBALS['TL_CONFIG']['addLanguageToUrl']) {

            $message = $GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_add_language_to_url'];

            $GLOBALS['TL_DCA']['tl_page']['list']['sorting']['breadcrumb'] .=
                '<div id="i18nl10n_message">' . $message . '</div>';
        };
}


}//end class tl_page_i18nl10n
