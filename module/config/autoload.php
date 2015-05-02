<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
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
	'Verstaerker\I18nl10n\Classes\I18nl10n'                        => 'system/modules/i18nl10n/classes/I18nl10n.php',
	'Verstaerker\I18nl10n\Classes\I18nl10nFrontend'                => 'system/modules/i18nl10n/classes/I18nl10nFrontend.php',
	'Verstaerker\I18nl10n\Classes\I18nl10nHook'                    => 'system/modules/i18nl10n/classes/I18nl10nHook.php',

	// Modules
	'Verstaerker\I18nl10n\Modules\ModuleI18nl10nLanguageSelection' => 'system/modules/i18nl10n/modules/ModuleI18nl10nLanguageSelection.php',

	// Pages
	'Verstaerker\I18nl10n\Pages\PageI18nl10nRegular'               => 'system/modules/i18nl10n/pages/PageI18nl10nRegular.php',

	// Widgets
	'Verstaerker\i18nl10n\Widgets\I18nl10nMetaWizard'              => 'system/modules/i18nl10n/widgets/I18nl10nMetaWizard.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'lang_default'        => 'system/modules/i18nl10n/templates',
	'lang_select'         => 'system/modules/i18nl10n/templates',
	'mod_i18nl10n_nav'    => 'system/modules/i18nl10n/templates',
	'mod_search_i18nl10n' => 'system/modules/i18nl10n/templates',
	'nav_i18nl10n'        => 'system/modules/i18nl10n/templates',
	'nav_l10n'            => 'system/modules/i18nl10n/templates',
));
