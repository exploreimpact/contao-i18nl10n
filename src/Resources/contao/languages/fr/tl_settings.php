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
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParam'][0] = 'Ajouter la langue à l\'URL';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParam'][1] =
    'Définissez comment la langue apparaîtra dans l\'URL des sites.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['parameter'] = 'Comme un paramètre (ex. ?language=en)';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['alias'] = 'Comme une partie de l\'alias (ex. accueil.fr.html) [Ne fonctionne pas actuellement]';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['url'] = 'Comme une partie de l\'URL (ex. mondomaine.com/fr/index.html)';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_aliasSuffixError'] =
    'Il n\'est pas possible d\'utiliser <em>"%s"</em> et <em>"%s"</em> en même temps. Veuillez n\'en sélectionner qu\'un !';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_contaoAddLanguageToUrlError'] =
    'I18nl10n ne supporte pas la fonctionnalité <em>"%s"</em> de Contao. Veuillez utiliser le module alternatif à la place.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingInfo'] =
    'La langue par défaut est absente des langues supporées pour votre page et a donc été ajoutée automatiquement.';