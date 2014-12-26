<?php
/**
 * i18nl10n Contao Module
 *
 * PHP version 5
 *
 * @copyright   &copy; 2015 Verstärker, Patric Eberle 2014
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['module_i18nl10n'] = 'Multilingual content (i18nl10n)';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_languages'] = array
(
    'Site Languages',
    'Define <a target="_blank" href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"><em>valid ISO 639-1</em></a> language codes (e.g. <em>en</em> or <em>bg</em>) which represent the languages of your site. The default language <strong>must</strong> also be added here.'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_default_language'] = array
(
    'Default Language (Fallback)',
    'This is the language of your root page and the default language used by i18nl10n.'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffix'] = array
(
    'Use language as alias suffix',
    'The corresponding language will be appended on the fly to the current page alias (e.g. home &gt; home.en). URL suffix will be appended after it (e.g. home.en.html). Note: This will change dynamically generated links in menus, but not your page aliases!'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_addLanguageToUrl'] = array
(
    'Add the language to the URL',
    'Add the language string as first URL parameter (e.g. <em>http://domain.tld/en/</em>). Works the same way as the core feature <em>"Add the language to the URL"</em>. Note!: If you enable this, the core feature and <em>"%s"</em> <em>must</em> be disabled!'
);

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_alias_suffixError'] =
    'It\'s not possible to use <em>"%s"</em> and <em>"%s"</em> at the same time. Please only select one!';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_contaoAddLanguageToUrlError'] =
    'I18nl10n does not support the <em>"%s"</em> feature of Contao. Please us the module alternative instead.';

$GLOBALS['TL_LANG']['tl_settings']['i18nl10n_defLangMissingInfo'] =
    'The default language was missing inside the supported languages for your page and therefore added automatically.';