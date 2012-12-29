<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    System
 * @license    LGPL
 * @filesource
 */


/**
 * Class I18nL10nHooks
 *
 * Provide Hooks to modify Contao
 * behaviour related to I18N and L10N.
 * @copyright  Krasimir Berov 2010-2012
 * @author     Krasimir Berov 
 * @package    MultiLanguagePage
 * 
 */
class I18nL10nHooks extends System
{
  /**
   * Generates url for the site according to settings from the backend.
   * 
   * Assumptions:
   * $GLOBALS['TL_CONFIG']['addLanguageToUrl'] == false;
   * $GLOBALS['TL_CONFIG']['useAutoItem'] == false;
   * TODO: create our own auto_item?
   * 
   * 
   */
  public function generateFrontendUrl($arrRow, $strParams, $strUrl){

    if(!is_array($arrRow)){
      throw new Exception('not an associative array.');
    }
    $language = (array_key_exists('robots',$arrRow) ?
                 $GLOBALS['TL_LANGUAGE']:
                 $arrRow['language']);
    if(!$language) $language = $GLOBALS['TL_LANGUAGE'];
    $alias = $arrRow['alias'];
    
    if($GLOBALS['TL_CONFIG']['i18nl10n_alias_suffix']) {
      if($strUrl)
        $mystrUrl = preg_replace(
          "/$alias(\.{$language})?/u",
          $alias.'.'.$language,
          $strUrl,
          1 //limit to one match
        );
      else 
      $mystrUrl = $alias.'.'.$language.$GLOBALS['TL_CONFIG']['urlSuffix'];
      //TODO: useAutoItem $GLOBALS['TL_CONFIG']['useAutoItem'] ?
    }
    elseif($GLOBALS['TL_CONFIG']['i18nl10n_addLanguageToUrl']){
      if($strUrl){
        //issue #56 remove language parameter from request path
        $mystrUrl = preg_replace(
          "@\/language\/{$language}@", '', $strUrl
        );
        $mystrUrl = $language.'/'.$mystrUrl;
      }
      else {
        $mystrUrl = $language.'/'
        .$alias
        .$GLOBALS['TL_CONFIG']['urlSuffix'];
      }
    }
    else {
      $mystrUrl = $strUrl.($strParams=='?language='.$language?
                   $strParams:
                   $strParams.'?language='.$language);
    }
    return $mystrUrl;
  }
  
  public function getPageIdFromUrl(Array $fragments) {
    global $TL_CONFIG;
    $this->import('Database');
    $fragments = array_map('urldecode', $fragments);
    $languages = deserialize($TL_CONFIG['i18nl10n_languages']);
    $language = $TL_CONFIG['i18nl10n_default_language'];
    //error_log( __METHOD__.':'.var_export($fragments,true) );
    
    if($TL_CONFIG['i18nl10n_addLanguageToUrl']){
      if(preg_match('@^([A-z]{2})$@i',$fragments[0],$matches)){
        $language = strtolower($matches[1]);
        array_push($fragments,'language',$language);
      }
      $i = ($fragments[1] == 'auto_item'?2:1);
      $fragments[$i] =($fragments[$i]?$fragments[$i]:$TL_CONFIG['i18nl10n_default_page']);
      if(preg_match('@^([\-\p{L}\p{N}\.]+)$@iu',$fragments[$i],$matches)){
          $fragments[0] = $fragments[$i];
          
      }
      //TODO: solve "auto_item" issue 
      $fragments = array_delete($fragments,$i);
    }
    elseif($TL_CONFIG['i18nl10n_alias_suffix']){
      $ok = preg_match('/^([\-\p{L}\p{N}\.]+)\.([A-z]{2})$/u',$fragments[0],$matches);
      if($ok) {
          $language = strtolower($matches[2]); 
      }
      if($ok &&  in_array($language,$languages)){
          $fragments[0] = $matches[1];
          array_push($fragments,'language',$language);
      }
    }
    $time = time();
    $objAlias = $this->Database->prepare("
SELECT alias FROM tl_page WHERE
(id=(SELECT pid FROM tl_page_i18nl10n WHERE id=? AND language=?)
OR id=(SELECT pid FROM tl_page_i18nl10n WHERE alias=? AND language=?))"
. (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time)
   AND (stop='' OR stop>$time) AND published=1" : ""))
->execute(
  (is_numeric($fragments[0]) ? $fragments[0] : 0),
  $language, $fragments[0],$language
);
    if($objAlias->numRows){
      $fragments[0] = $objAlias->alias;
    }
    
    return $fragments;
}

/**
 *TODO if needed
 * function getRootPageFromUrl(){
 *   error_log( __METHOD__.':'.var_export($_GET,true) );
 *   return;
 * }
 */

}//end class
