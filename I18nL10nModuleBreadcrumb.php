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


/**
 * Class I18nL10nModuleBreadcrumb
 *
 * @copyright  Krasimir Berov 2010-2013
 * @author     Krasimir Berov
 * @package    MultiLanguagePage
 *
 * Just replaces mod_bredcrumb template
 * TODO: Remove this class and create a hook
 * See: $GLOBALS['TL_HOOKS']['generateBreadcrumb']
 */

class I18nL10nModuleBreadcrumb extends ModuleBreadcrumb
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_i18nl10nbreadcrumb';
    protected $time;
    protected $default_language;
    protected function compile()
    {
        global $objPage;
        $this->time = time();
        $this->default_language = $GLOBALS['TL_CONFIG']['i18nl10n_default_language'];
        $pages = array();
        $items = array();
        $ids   = array();
        $pageId = $objPage->id;
        $type = null;
        //Get path to root in one go
        $objPages = $this->Database->prepare("
        SELECT @start_id as _id,
             (select @start_id := pp.pid FROM tl_page pp WHERE pp.id=_id) as pid
        FROM tl_page p, (SELECT @start_id :=?) as start
        where @start_id >0 ")
        ->limit(20)//max levels up to root
        ->execute($pageId);
        foreach($objPages->fetchAllassoc() as $row) { array_push($ids,$row['_id']); }

        //Now get pages L10Ns
        $with_l10n = ($GLOBALS['TL_LANGUAGE']!=$this->default_language);
        $language_sql = ($with_l10n?
        " AND (i.language = '".$GLOBALS['TL_LANGUAGE']."' OR i.language IS NULL) ":"");
        if($with_l10n) {
        $sql = "
SELECT p.id,
(CASE i.alias
    WHEN NULL THEN p.alias
    WHEN ''   THEN p.alias
    ELSE      i.alias
 END) AS alias,
p.type, p.published, i.language,
(CASE i.title WHEN NULL THEN p.title ELSE i.title END) as title,
(CASE i.pageTitle WHEN NULL THEN p.pageTitle ELSE i.pageTitle END) as pageTitle
FROM tl_page p
LEFT JOIN tl_page_i18nl10n i ON p.id = i.pid
WHERE p.id IN (".implode(',',array_reverse($ids)).")
$language_sql
".(!BE_USER_LOGGED_IN ? " AND (p.start='' OR p.start<$this->time) AND (p.stop='' OR p.stop>$this->time)
AND p.published=1" : "");
        }
        else {
        $sql = "
            SELECT
                id, alias, title, type, pageTitle, published
            FROM
                tl_page
            WHERE
                id IN (".implode(',',array_reverse($ids)).") "
                . (!BE_USER_LOGGED_IN ? "
                    AND (start='' OR start<$this->time)
                    AND (stop='' OR stop>$this->time)
                    AND published=1" : "") . "
            ORDER BY
                FIELD(id," . implode(',', array_reverse($ids)) . ")";
        }

        $pages = $this->Database->prepare($sql)->limit(20)->execute(
                                )->fetchAllassoc();
       // Build breadcrumb menu
       $this->Template->items = $this->buildBreadcrumbMenu($pages);
    }


    private function buildBreadcrumbMenu(Array $pages) {
        global $TL_LANGUAGE;
        $with_l10n = ($TL_LANGUAGE!=$this->default_language);
        $items = array();
        $root_page = array_shift($pages);

        // Get first page
        if($with_l10n):
            $sql = "
                SELECT
                    p.id,
                (CASE i.alias
                    WHEN NULL THEN p.alias
                    WHEN ''   THEN p.alias
                    ELSE      i.alias
                    END) AS alias,
                (CASE i.title
                    WHEN NULL THEN p.title
                    ELSE i.title
                    END) AS title,
                (CASE i.pageTitle
                    WHEN NULL THEN p.pageTitle
                    ELSE i.pageTitle
                    END) AS pageTitle
                FROM
                    tl_page p
                LEFT JOIN
                    tl_page_i18nl10n i
                        ON
                            p.id = i.pid
                WHERE
                    p.pid=?
                    AND p.type IN ('regular','forward')
                    AND (i.language = '".$TL_LANGUAGE."' OR i.language IS NULL) " . (!BE_USER_LOGGED_IN ? "
                    AND (p.start='' OR p.start<$this->time)
                    AND (p.stop='' OR p.stop>$this->time) AND p.published=1" : "") . "
                ORDER BY
                    p.sorting";
        else:
            $sql = "
                SELECT
                    id, alias, title, pageTitle
                FROM
                    tl_page
                WHERE
                    pid=?
                    AND type IN ('regular','forward') " . (!BE_USER_LOGGED_IN ? "
                    AND (start='' OR start<$this->time)
                    AND (stop='' OR stop>$this->time)
                    AND published=1" : "") . "
                ORDER BY sorting";
        endif;
        $objFirstPage = $this->Database->prepare($sql)->limit(1)
                                       ->execute($root_page['id']);
        $first_page = $objFirstPage->fetchAssoc();
        $root_title = (strlen($first_page['pageTitle']) ?
            specialchars($first_page['pageTitle']) : specialchars($first_page['title']));
        $items_href =$this->generateFrontendUrl($first_page);

        $items[] = array
        (
            'isRoot' => true,
            'isActive' => false,
            'href' => (!empty($first_page) ? $items_href : $this->Environment->base),
            'title' => $root_title,
            'link' => $root_title
        );

        $c = count($pages)-1;
        for($i=0; $i <$c; $i++){
            if (($pages[$i]['hide'] && !$this->showHidden) || (!$pages[$i]['published'] && !BE_USER_LOGGED_IN))
            {
                continue;
            }

            // Get href
            switch ($pages[$i]['type'])
            {
                case 'redirect':
                    $href = $pages[$i]['url'];

                    if (strncasecmp($href, 'mailto:', 7) === 0)
                    {
                        $this->import('String');
                        $href = $this->String->encodeEmail($href);
                    }
                    break;

                case 'forward':
                    $objNext = $this->Database->prepare("
                        SELECT
                            id, alias
                        FROM
                            tl_page
                        WHERE
                            id=?
                    ")->limit(1)
                    ->execute($pages[$i]['jumpTo']);

                    if ($objNext->numRows)
                    {
                        $item = $objNext->fetchAssoc();
                        $item['language'] = $pages[$i]['language'];
                        $href = $this->generateFrontendUrl($item);
                        break;
                    }
                    // DO NOT ADD A break; STATEMENT

                default:
                    $href = $this->generateFrontendUrl($pages[$i]);
                    break;
            }
            $items[] = array
            (
                'isRoot' => false,
                'isActive' => false,
                'href' => $href,
                'title' => ($pages[$i]['pageTitle']!='' ? specialchars($pages[$i]['pageTitle']) : specialchars($pages[$i]['title'])),
                'link' => $pages[$i]['title']
            );
        }//end foreach
        $pageTitle = $pages[$c]['pageTitle'];
        $title = $pages[$c]['title'];
        // Active article
        if (strlen($this->Input->get('articles')))
        {
            $items[] = array
            (
                'isRoot' => false,
                'isActive' => false,
                'href' => $this->generateFrontendUrl($pages[$c]),
                'title' => (strlen($pageTitle) ? specialchars($pageTitle) : specialchars($title)),
                'link' => $title
            );

            list($strSection, $strArticle) = explode(':', $this->Input->get('articles'));

            if (is_null($strArticle))
            {
                $strArticle = $strSection;
            }

            // Get article title
            $objArticle = $this->Database->prepare(
            //TODO: Localize articles too someday
                "SELECT
                 title FROM tl_article WHERE id=? OR alias=?
                AND (language=? OR language='')
                ")
                ->limit(1)
                ->execute(
                    (is_numeric($strArticle) ? $strArticle : 0),
                    $strArticle,$GLOBALS['TL_LANGUAGE']);

            if ($objArticle->numRows)
            {
                $items[] = array
                (
                    'isRoot' => false,
                    'isActive' => true,
                    'title' => specialchars($objArticle->title),
                    'link' => $objArticle->title
                );
            }

        }
        // Active page
        else
        {
            $items[] = array
            (
                'isRoot' => false,
                'isActive' => true,
                'href' => $this->generateFrontendUrl($pages[$c]),
                'title' => (strlen($pageTitle) ? specialchars($pageTitle) : specialchars($title)),
                'link' => $title
            );
        }

        return $items;
    }//end buildBreadcrumbMenu
}