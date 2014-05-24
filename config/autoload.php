<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package I18nl10n
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Verstaerker',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Verstaerker\I18nl10n\Classes\I18nl10nFrontend'    => 'system/modules/i18nl10n/classes/I18nl10nFrontend.php',
	'Verstaerker\I18nl10n\Classes\I18nl10nHooks'       => 'system/modules/i18nl10n/classes/I18nl10nHooks.php',

	// Modules
	'Verstaerker\I18nl10n\Pages\ModuleI18nL10nArticle' => 'system/modules/i18nl10n/modules/ModuleI18nL10nArticle.php',
	'ModuleI18nL10nLanguageNavigation'                 => 'system/modules/i18nl10n/modules/ModuleI18nL10nLanguageNavigation.php',

	// Pages
	'Verstaerker\I18nl10n\Pages\PageI18nL10nRegular'   => 'system/modules/i18nl10n/pages/PageI18nL10nRegular.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'lang_default'     => 'system/modules/i18nl10n/templates',
	'mod_i18nl10n_nav' => 'system/modules/i18nl10n/templates',
));
