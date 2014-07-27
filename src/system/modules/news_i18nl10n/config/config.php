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
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */

/**
 * Insert be module to sidebar
 */
if(array_search("news", array_keys($GLOBALS['BE_MOD']['content']))) {
    array_insert(
        $GLOBALS['BE_MOD']['content'],
        array_search("news", array_keys($GLOBALS['BE_MOD']['content'])) + 1,
        array
        (
            'news_i18nl10n' => array
            (
                'tables' => array('tl_news_i18nl10n'),
                'icon'   => 'system/modules/core_i18nl10n/assets/img/i18nl10n.png'
            )
        )
    );
}

// Insert be module as part of tl_news
$GLOBALS['BE_MOD']['content']['news']['tables'][] = 'tl_news_i18nl10n';


/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['FE_MOD']['i18nl10n']['i18nl10nNewsArchive'] = '\I18nl10n\Modules\ModuleI18nl10nNewsArchive';
$GLOBALS['FE_MOD']['i18nl10n']['i18nl10nNewsReader'] = '\I18nl10n\Modules\ModuleI18nl10nNewsReader';


// add hook for ajax requests
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('tl_news_l10n', 'executePostActions');