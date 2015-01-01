<?php
/**
 * i18nl10n Contao Module
 *
 * PHP version 5
 *
 * @copyright   2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['module_i18nl10n'] = 'Multilingual content (i18nl10n)';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParam'] = array
(
    'Add language to url',
    'Define how the language will appear in the websites urls.'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['parameter'] = 'as parameter (f.ex. ?language=en)';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['alias'] = 'as part of alias (f.ex. home.en.html)';
$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_urlParamLabels']['url'] = 'as part of url (f.ex. mypage.com/en/index.html)';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'] = array
(
    '%s',
    'Disabled by i18nl10n module. Please use module alternative.'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_aliasSuffixError'] =
    'It\'s not possible to use <em>"%s"</em> and <em>"%s"</em> at the same time. Please only select one!';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_contaoAddLanguageToUrlError'] =
    'I18nl10n does not support the <em>"%s"</em> feature of Contao. Please us the module alternative instead.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingInfo'] =
    'The default language was missing inside the supported languages for your page and therefore added automatically.';