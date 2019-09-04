<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Verstaerker\I18nl10nBundle\Model\PageI18nl10nModel;

class GetRootPageFromUrlHook extends System
{
    /** @var Request */
    protected $request;


    public function __construct()
    {
        parent::__construct();

        $this->import('contao.framework', 'framework');
        $this->import('request_stack', 'request_stack');
        $this->request = $this->request_stack->getCurrentRequest();
    }


    public function getRootPageFromUrl()
    {
        /** @var \Contao\PageModel $pageModel */
        $pageModel = $this->framework->getAdapter(\Contao\PageModel::class);

        $httpHost = $this->request->getHttpHost();

        // Get the matching root pages
        $rootPages = [];
        $pages = $pageModel->findBy(["(tl_page.type='root' AND (tl_page.dns=? OR tl_page.dns=''))"], $httpHost);
        if ($pages instanceof Collection) {
            $rootPages = $pages->getModels();
        }

        // Try all root pages
        foreach ($rootPages as $rootPage) {

            // Try default pages
            $defaultIndexPageOfRoot = $pageModel->findBy(["tl_page.pid='" . $rootPage->id . "' AND tl_page.language='" . $GLOBALS["TL_LANGUAGE"] . "'"], null, [
                'limit'  => 1,
                'return' => 'Model',
                'order' => 'sorting ASC'
            ]);
            if ($defaultIndexPageOfRoot instanceof Model) {
                return $defaultIndexPageOfRoot;
            }



            // Try i18nl10n pages
            $database = \Database::getInstance();
            $sql = "SELECT *
                 FROM tl_page_i18nl10n
                 WHERE
                    pid = ?
                    AND
                    language = ?
                ORDER BY sorting ASC
                LIMIT 1
            ";

            /** @var Result $objL10nPage */
            $objI18nl10nPage = $database
                ->prepare($sql)
                ->execute(
                    $rootPage->id,
                    $GLOBALS["TL_LANGUAGE"]
                );

            if ($objI18nl10nPage instanceof Result) {
                $objI18nIndexPage = new PageModel();
                foreach ($objI18nl10nPage->fetchAssoc() as $property => $value) {
                    $objI18nIndexPage->$property = $value;
                }



                // @todo:   Wrong root page found! We nned to get the page with ID 2!!!



                echo "<pre>";
                var_dump($objI18nIndexPage);
                die();



                return $objI18nIndexPage;
            }
        }

        return null;
    }
}
