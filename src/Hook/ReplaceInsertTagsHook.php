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
        return I18nl10n::replaceI18nl10nInsertTags($strTag);
    }
}
