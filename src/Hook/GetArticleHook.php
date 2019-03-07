<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class GetArticleHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * Determine when to show content elements in relation to the current page language.
 */
class GetArticleHook
{
    /**
     * Check if the current article has visible elements and return an empty template if not
     * @param  [ArticleModel] $objRow Article row
     * @return [nothing]
     */
    public function checkIfEmpty($objRow)
    {
        $objElements = \ContentModel::findPublishedByPidAndTable($objRow->id, "tl_article");
        
        $blnDisplay = false;
        if ($objElements && $objElements->count() > 0) {
            while ($objElements->next()) {
                if ($objElements->language == "" || $objElements->language == $GLOBALS['TL_LANGUAGE']) {
                    $blnDisplay = true;
                    break;
                }
            }
        }

        if (!$blnDisplay) {
            $objRow->customTpl = "mod_i18nl10n_article_empty";
        }
    }
}
