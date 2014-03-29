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
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Class I18nL10nModuleArticle 
 *
 * @copyright  Krasimir Berov 2010-2013
 * @author     Krasimir Berov 
 * @package    Controller
 * The only thing changed here is the SQL Query.
 * We should keep an eye on how this part of the core is changing.
 * TODO: Ask Leo to make the query changeable from extensions.
 
 */
class I18nL10nModuleArticle extends ModuleArticle
{

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;
		$this->import('String');

		if ($this->blnNoMarkup)
		{
			$this->Template = new FrontendTemplate('mod_article_plain');
			$this->Template->setData($this->arrData);
		}

		$alias = ($this->alias != '') ? $this->alias : $this->title;

		if (in_array($alias, array('header', 'container', 'left', 'main', 'right', 'footer')))
		{
			$alias .= '-' . $this->id;
		}

		$alias = standardize($alias);

		// Generate the cssID if it is not set
		if ($this->cssID[0] == '')
		{
			$this->cssID = array($alias, $this->cssID[1]);
		}

		$this->Template->column = $this->inColumn;

		// Add modification date
		$this->Template->timestamp = $this->tstamp;
		$this->Template->date = $this->parseDate($objPage->datimFormat, $this->tstamp);
		$this->Template->author = $this->author;
		// Clean the RTE output
		if ($objPage->outputFormat == 'xhtml')
		{
			$this->teaser = $this->String->toXhtml($this->teaser);
		}
		else
		{
			$this->teaser = $this->String->toHtml5($this->teaser);
		}

		// Show teaser only
		if ($this->multiMode && $this->showTeaser)
		{
			$this->Template = new FrontendTemplate('mod_article_teaser');
			$this->Template->setData($this->arrData);

			$this->cssID = array($alias, '');
			$arrCss = deserialize($this->teaserCssID);

			// Override the CSS ID and class
			if (is_array($arrCss) && count($arrCss) == 2)
			{
				if ($arrCss[0] == '')
				{
					$arrCss[0] = $alias;
				}

				$this->cssID = $arrCss;
			}

			$article = (!$GLOBALS['TL_CONFIG']['disableAlias'] && $this->alias != '') ? $this->alias : $this->id;
			$href = 'articles=' . (($this->inColumn != 'main') ? $this->inColumn . ':' : '') . $article;

			$this->Template->headline = $this->headline;
			$this->Template->href = $this->addToUrl($href);
			$this->Template->teaser = $this->teaser;
			$this->Template->readMore = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true);
			$this->Template->more = $GLOBALS['TL_LANG']['MSC']['more'];

			return;
		}

		// Get section and article alias
		list($strSection, $strArticle) = explode(':', $this->Input->get('articles'));

		if ($strArticle === null)
		{
			$strArticle = $strSection;
		}

		// Overwrite the page title
		if (!$this->blnNoMarkup && $strArticle != '' && ($strArticle == $this->id || $strArticle == $this->alias) && $this->title != '')
		{
			$objPage->pageTitle = strip_insert_tags($this->title);
		}

		$this->Template->printable = false;
		$this->Template->backlink = false;

		// Back link
		if (!$this->multiMode && $strArticle != '' && ($strArticle == $this->id || $strArticle == $this->alias))
		{
			$this->Template->back = specialchars($GLOBALS['TL_LANG']['MSC']['goBack']);
			// Remove the "/articles/…" part from the URL
			if ($GLOBALS['TL_CONFIG']['disableAlias'])
			{
				$this->Template->backlink = preg_replace('@&(amp;)?articles=[^&]+@', '', $this->Environment->request);
			}
			else
			{
				$this->Template->backlink = preg_replace('@/articles/[^/]+@', '', $this->Environment->request) . $GLOBALS['TL_CONFIG']['urlSuffix'];
			}
		}

		$arrElements = array();

		// Get all visible content elements
		$objCte = $this->Database->prepare(
           "SELECT id FROM tl_content WHERE pid=?
           AND (language=? OR language='') " 
           . (!BE_USER_LOGGED_IN ? " AND invisible=''" : "") . " ORDER BY sorting")
								 ->execute($this->id,$GLOBALS['TL_LANGUAGE']);

		while ($objCte->next())
		{
			$arrElements[] = $this->getContentElement($objCte->id);
		}

		$this->Template->teaser = $this->teaser;
		$this->Template->elements = $arrElements;

		if ($this->keywords != '')
		{
			$GLOBALS['TL_KEYWORDS'] .= (strlen($GLOBALS['TL_KEYWORDS']) ? ', ' : '') . $this->keywords;
		}

		// Backwards compatibility
		if ($this->printable == 1)
		{
			$this->Template->printable = true;
			$this->Template->pdfButton = true;
		}

		// New structure
		elseif ($this->printable != '')
		{
			$options = deserialize($this->printable);

			if (is_array($options) && !empty($options))
			{
				$this->Template->printable = true;
				$this->Template->printButton = in_array('print', $options);
				$this->Template->pdfButton = in_array('pdf', $options);
				$this->Template->facebookButton = in_array('facebook', $options);
				$this->Template->twitterButton = in_array('twitter', $options);
			}
		}

		// Add syndication variables
		if ($this->Template->printable)
		{
			$request = $this->getIndexFreeRequest(true);

			$this->Template->print = '#';
			$this->Template->encUrl = rawurlencode($this->Environment->base . $this->Environment->request);
			$this->Template->encTitle = rawurlencode($objPage->pageTitle);
			$this->Template->href = $request . ((strpos($request, '?') !== false) ? '&amp;' : '?') . 'pdf=' . $this->id;

			$this->Template->printTitle = specialchars($GLOBALS['TL_LANG']['MSC']['printPage']);
			$this->Template->pdfTitle = specialchars($GLOBALS['TL_LANG']['MSC']['printAsPdf']);
			$this->Template->facebookTitle = specialchars($GLOBALS['TL_LANG']['MSC']['facebookShare']);
			$this->Template->twitterTitle = specialchars($GLOBALS['TL_LANG']['MSC']['twitterShare']);
		}
	}
}//end class

?>