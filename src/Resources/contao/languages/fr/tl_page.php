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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['module_i18nl10n'] = 'Paramètres i18nl10n';

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_published'] = array
(
    'Publier L10N',
    'Publier cette traduction.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_localizations'] = array
(
    'Langues du site',
    'Langues/traductions additionnelles disponibles pour cette arborescence.'
);

$GLOBALS['TL_LANG']['tl_page']['i18nl10n_language'] = 'Langue';

/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_page']['msg_no_languages']  =
    'Aucune langue alternative n\'a été définie. Veuillez le faire sur les %s paramètres %s page.';
$GLOBALS['TL_LANG']['tl_page']['msg_multiple_root'] =
    'i18nl10n a découvert plusieurs racines de site dans votre arborescence. Faites attention : ce module ne peut gérer plusieurs arborescences !';
$GLOBALS['TL_LANG']['tl_page']['msg_missing_dns']  =
    'Quand on utilise plus d\'une racine de site avec i18nl10n, chaque racine de site nécessite un domaine unique ! Cette valeur est absente dans une ou plusieurs racines !';
$GLOBALS['TL_LANG']['tl_page']['msg_duplicated_dns']  =
    'Certaines racines de site utilisent le même nom de domaine. Quand on utilise le module i18nl10n, seule une racine de site est autorisée pour un domaine.';