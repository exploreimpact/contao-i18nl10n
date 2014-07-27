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
 * Run in a custom namespace, so the class can be replaced
 */
namespace Verstaerker\I18nl10n\Modules;


/**
 * Class ModuleI18nl10nNewsArchive
 *
 * I18nl10n front end module "news archive".
 */
class ModuleI18nl10nNewsArchive extends \ModuleNews
{

    // TODO: Everything

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newsarchive';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsarchive'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

		// No news archives available
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			return '';
		}

		// Show the news reader if an item has been selected
		if ($this->i18nl10n_news_readerModule > 0 && (isset($_GET['items']) || ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->i18nl10n_news_readerModule, $this->strColumn);
		}

		// Hide the module if no period has been selected
		if ($this->news_jumpToCurrent == 'hide_module' && !isset($_GET['year']) && !isset($_GET['month']) && !isset($_GET['day']))
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;

        \FB::info('compile i18nl10n news archive');

        $limit = null;
		$offset = 0;
		$intBegin = 0;
		$intEnd = 0;

		// Jump to the current period
		if (!isset($_GET['year']) && !isset($_GET['month']) && !isset($_GET['day']) && $this->news_jumpToCurrent != 'all_items')
		{
			switch ($this->news_format)
			{
				case 'news_year':
					\Input::setGet('year', date('Y'));
					break;

				default:
				case 'news_month':
					\Input::setGet('month', date('Ym'));
					break;

				case 'news_day':
					\Input::setGet('day', date('Ymd'));
					break;
			}
		}

		// Display year
		if (\Input::get('year'))
		{
			$strDate = \Input::get('year');
			$objDate = new \Date($strDate, 'Y');
			$intBegin = $objDate->yearBegin;
			$intEnd = $objDate->yearEnd;
			$this->headline .= ' ' . date('Y', $objDate->tstamp);
		}
		// Display month
		elseif (\Input::get('month'))
		{
			$strDate = \Input::get('month');
			$objDate = new \Date($strDate, 'Ym');
			$intBegin = $objDate->monthBegin;
			$intEnd = $objDate->monthEnd;
			$this->headline .= ' ' . \Date::parse('F Y', $objDate->tstamp);
		}
		// Display day
		elseif (\Input::get('day'))
		{
			$strDate = \Input::get('day');
			$objDate = new \Date($strDate, 'Ymd');
			$intBegin = $objDate->dayBegin;
			$intEnd = $objDate->dayEnd;
			$this->headline .= ' ' . \Date::parse($objPage->dateFormat, $objDate->tstamp);
		}
		// Show all items
		elseif ($this->news_jumpToCurrent == 'all_items')
		{
			$intBegin = 0;
			$intEnd = time();
		}

		$this->Template->articles = array();

        // TODO: move this behind request
		// Split the result
		if ($this->perPage > 0)
		{
			// Get the total number of items
			$intTotal = \NewsModel::countPublishedFromToByPids($intBegin, $intEnd, $this->news_archives);

			if ($intTotal > 0)
			{
				$total = $intTotal;

				// Get the current page
				$id = 'page_a' . $this->id;
				$page = \Input::get($id) ?: 1;

				// Do not index or cache the page if the page number is outside the range
				if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
				{
					global $objPage;
					$objPage->noSearch = 1;
					$objPage->cache = 0;

					// Send a 404 header
					header('HTTP/1.1 404 Not Found');
					return;
				}

				// Set limit and offset
				$limit = $this->perPage;
				$offset = (max($page, 1) - 1) * $this->perPage;

				// Add the pagination menu
				$objPagination = new \Pagination($total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id);
				$this->Template->pagination = $objPagination->generate("\n  ");
			}
		}

		// Get the news items
		if (isset($limit))
		{
			$objArticles = \NewsModel::findPublishedFromToByPids($intBegin, $intEnd, $this->news_archives, $limit, $offset);
		}
		else
		{
			$objArticles = \NewsModel::findPublishedFromToByPids($intBegin, $intEnd, $this->news_archives);
		}

		// No items found
		if ($objArticles === null)
		{
			$this->Template = new \FrontendTemplate('mod_newsarchive_empty');
		}
		else
		{
            $objArticles = self::getL10nArticles($objArticles);

			$this->Template->articles = self::parseL10nArticles($objArticles);
		}

		$this->Template->headline = trim($this->headline);
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
		$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['empty'];
	}


    /**
     * Get related translations for news items
     *
     * @param $objArticles
     * @return mixed
     */
    protected function getL10nArticles($objArticles)
    {

        if($GLOBALS['TL_LANGUAGE'] != $GLOBALS['TL_CONFIG']['i18nl10n_default_language'])
        {
            while($objArticles->next()) {
                $sql = "
                    SELECT
                      id,pid,headline,alias,author,subheadline,teaser,addImage,
                      singleSRC,alt,size,imagemargin,imageUrl,fullsize,
                      caption,floating,addEnclosure,enclosure,source,jumpTo,
                      articleId,url,target,language,l10n_published,start,stop
                    FROM
                      tl_news_i18nl10n
                    WHERE
                      pid = ?
                      AND language = ?
                ";

                if(!BE_USER_LOGGED_IN)
                {
                    $time = time();
                    $sql .= "
                        AND (start = '' OR start < $time)
                        AND (stop = '' OR stop > $time)
                        AND l10n_published = 1
                    ";
                }

                $objL10n = \Database::getInstance()
                    ->prepare($sql)
                    ->limit(1)
                    ->execute($objArticles->id, $GLOBALS['TL_LANGUAGE']);

                if($objL10n->numRows)
                {

                    $arrL10n = $objL10n->fetchAllAssoc();

                    foreach($arrL10n[0] as $key => $value)
                    {
                        $objArticles->$key = $value;
                    }

                }
            }

        }

        return $objArticles->reset();
    }


    /**
     * Parse one or more news item
     *
     * @param $objArticles
     * @param bool $blnAddArchive
     * @return array
     */
    protected function parseL10nArticles($objArticles, $blnAddArchive=false)
    {
        $limit = 0;

        while($objArticles->next())
        {
            if($objArticles->l10n_published == 1) {
                $limit++;
            }
        }

        $objArticles->reset();

        if ($limit < 1)
        {
            return array();
        }

        $count = 0;
        $arrArticles = array();

        while ($objArticles->next())
        {
            if($objArticles->l10n_published == 1)
            {
                $arrArticles[] = $this->parseArticle($objArticles, $blnAddArchive, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
            }
        }

        return $arrArticles;
    }
}
