<?php
/**
 * i18nl10n Contao Module
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Web ex Machina (FR Translation) <https://www.webexmachina.fr>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


/**
 * Legends & Fields
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_legend'] = 'Paramètres L10N';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['redirect_legend'] = 'Redirection'; // For some unknown reason this is not taken form tl_page

$GLOBALS['TL_LANG']['tl_page_i18nl10n'][''] = array
(
    '',
    ''
);

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_menuLegend']    = 'Champs traduits pour les menus et l\'adresse';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_metaLegend']    = 'Traduction des meta-données';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_timeLegend']    = 'Paramètres de traduction pour la date et l\'heure';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['i18nl10n_expertLegend']  = &$GLOBALS['TL_LANG']['tl_page']['expert_legend'];
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['publish_legend'] = &$GLOBALS['TL_LANG']['tl_page']['publish_legend'];

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['new'] = array
(
    'Nouvelle L10N',
    'Ajoutez une traduction pour la page'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['define_language'] = array
(
    'Langues',
    'Définissez les langues dans la page de configuration'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['edit'] = array
(
    'Éditer',
    'Éditer la traduction %s'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['copy'] = array
(
    'Dupliquer',
    'Dupliquer la traduction'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['delete'] = array
(
    'Supprimer',
    'Supprimer la traduction'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['toggle'] = array
(
    'Publier/Dépublier L10N',
    'Publier/Dépublier L10N ID %s'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['show'] = array
(
    'Afficher',
    'Afficher la traduction'
);

$GLOBALS['TL_LANG']['tl_page_i18nl10n']['localize_all'] = array
(
    'L10N pour tout',
    'Traduire toutes les pages ne comportant pas de traductions avec les langues disponibles'
);

/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_root'] =
    'Pas de racine de site définie. Veuillez le faire dans le menu "%s"';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_no_languages'] =
    'Pas de langues alternatives configurées pour i18nl10n. Veuillez le faire dans les racines de site du menu "%s".';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_some_languages'] =
    'Des racines de site n\'ont pas de langues alternatives.';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['msg_localize_all'] =
    'Je vais créer des traductions pour toutes les traductions manquantes basée sur la liste suivante (et sur les droits de l\'utilisateur). Êtes-vous sûr de vouloir continuer ?';
$GLOBALS['TL_LANG']['tl_page_i18nl10n']['no_languages'] = 'Pas de langues';