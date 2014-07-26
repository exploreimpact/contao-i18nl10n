<?php

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

// add hook for ajax requests
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('tl_news_l10n', 'executePostActions');