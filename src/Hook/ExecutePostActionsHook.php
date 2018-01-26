<?php

namespace Verstaerker\I18nl10nBundle\Hook;

/**
 * Class ExecutePostActionsHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * https://docs.contao.org/books/api/extensions/hooks/executePostActions.html
 */
class ExecutePostActionsHook
{
    /**
     * Handle ajax requests
     *
     * @param $strAction
     *
     * @return bool
     */
    public function executePostActions($strAction)
    {
        switch ($strAction) {
            case 'toggleL10n':
                $pageI18nl10n = new \tl_page_i18nl10n;
                $pageI18nl10n->toggleL10n(
                    \Input::post('id'),
                    \Input::post('state') == 1
                );
                break;
        }
    }
}
