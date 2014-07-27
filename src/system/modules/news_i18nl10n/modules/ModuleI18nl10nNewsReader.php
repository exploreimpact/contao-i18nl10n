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
 * Class ModuleNewsReader
 *
 * Front end module "news reader".
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class ModuleI18nl10nNewsReader extends \ModuleNews
{

    protected static $strTable = 'tl_news';

    /**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newsreader';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsreader'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		// Do not index or cache the page if no news item has been specified
		if (!\Input::get('items'))
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}

		$this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

		// Do not index or cache the page if there are no archives
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

        \FB::info('ModuleI18nl10nNewsReader::compile');

		global $objPage;

		$this->Template->articles = '';
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

		// Get the news item
		$objArticle = self::findPublishedByParentAndIdOrAlias(\Input::get('items'));

        \FB::log($objArticle);

		if ($objArticle === null)
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			// Send a 404 header
			header('HTTP/1.1 404 Not Found');
			$this->Template->articles = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
			return;
		}

		$arrArticle = $this->parseArticle($objArticle);
		$this->Template->articles = $arrArticle;

		// Overwrite the page title (see #2853 and #4955)
		if ($objArticle->headline != '')
		{
			$objPage->pageTitle = strip_tags(strip_insert_tags($objArticle->headline));
		}

		// Overwrite the page description
		if ($objArticle->teaser != '')
		{
			$objPage->description = $this->prepareMetaDescription($objArticle->teaser);
		}

		// HOOK: comments extension required
		if ($objArticle->noComments || !in_array('comments', \ModuleLoader::getActive()))
		{
			$this->Template->allowComments = false;
			return;
		}

//		$objArchive = $objArticle->getRelated('pid');
		$this->Template->allowComments = $objArchive->allowComments;

		// Comments are not allowed
		if (!$objArchive->allowComments)
		{
			return;
		}

		// Adjust the comments headline level
		$intHl = min(intval(str_replace('h', '', $this->hl)), 5);
		$this->Template->hlc = 'h' . ($intHl + 1);

		$this->import('Comments');
		$arrNotifies = array();

		// Notify the system administrator
		if ($objArchive->notify != 'notify_author')
		{
			$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
		}

		// Notify the author
		if ($objArchive->notify != 'notify_admin')
		{
			if (($objAuthor = $objArticle->getRelated('author')) !== null && $objAuthor->email != '')
			{
				$arrNotifies[] = $objAuthor->email;
			}
		}

		$objConfig = new \stdClass();

		$objConfig->perPage = $objArchive->perPage;
		$objConfig->order = $objArchive->sortOrder;
		$objConfig->template = $this->com_template;
		$objConfig->requireLogin = $objArchive->requireLogin;
		$objConfig->disableCaptcha = $objArchive->disableCaptcha;
		$objConfig->bbcode = $objArchive->bbcode;
		$objConfig->moderate = $objArchive->moderate;

		$this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_news', $objArticle->id, $arrNotifies);
	}


    public static function findPublishedByParentAndIdOrAlias($varId)
    {

        // TODO: Check if default language

        $sql = "
            SELECT
              *
            FROM
              tl_news_i18nl10n
            WHERE
              alias = ?
              OR id = ?
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

        $objArticle = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute($varId, $varId);

        return $objArticle;
    }
}
