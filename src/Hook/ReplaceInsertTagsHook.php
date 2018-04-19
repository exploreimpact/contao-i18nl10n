<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Verstaerker\I18nl10nBundle\Classes\I18nl10n;

/**
 * Class ReplaceInsertTagsHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * https://docs.contao.org/books/api/extensions/hooks/replaceInsertTags.html
 */
class ReplaceInsertTagsHook
{
    /**
     * replaceInsertTags hook
     *
     * @param $strTag
     *
     * @return bool|string
     */
    public function replaceInsertTags($strTag)
    {
        global $objPage;

        $arrArguments = explode('::', $strTag);

        if ($arrArguments[0] === 'i18nl10n' && $arrArguments[1] === 'link') {
            $objNextPage = I18nl10n::getInstance()->findL10nWithDetails($arrArguments[2], $GLOBALS['TL_LANGUAGE']);

            if ($objNextPage === null) {
                return false;
            }

            switch ($objNextPage->type) {
                case 'redirect':
                    $strUrl = \Controller::replaceInsertTags($objNextPage->url);

                    if (strncasecmp($strUrl, 'mailto:', 7) === 0) {
                        $strUrl = \StringUtil::encodeEmail($strUrl);
                    }
                    break;

                case 'forward':
                    $intForwardId = $objNextPage->jumpTo ?: \PageModel::findFirstPublishedByPid($objNextPage->id)
                        ->current()->id;

                    $objNext = \PageModel::findWithDetails($intForwardId);

                    if ($objNext !== null) {
                        $strUrl = I18nl10n::generateFrontendUrl($objNext->row(), null, '');
                        break;
                    }

                // no break
                default:
                    $strUrl = I18nl10n::generateFrontendUrl($objNextPage->row(), null, '');
                    break;
            }

            $strName = $objNextPage->title;
            $strTarget = $objNextPage->target ?
                (($objPage->outputFormat == 'xhtml') ? LINK_NEW_WINDOW : ' target="_blank"') : '';
            $strTitle = $objNextPage->pageTitle ?: $objNextPage->title;

            return sprintf(
                '<a href="%s" title="%s"%s>%s</a>',
                $strUrl,
                specialchars($strTitle),
                $strTarget,
                specialchars($strName)
            );
        }

        return false;
    }
}
