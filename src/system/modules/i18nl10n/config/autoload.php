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
    'Verstaerker\I18nl10n\Classes\I18nl10n'                        => 'system/modules/i18nl10n/classes/I18nl10n.php',
    'Verstaerker\I18nl10n\Classes\I18nl10nFrontend'                => 'system/modules/i18nl10n/classes/I18nl10nFrontend.php',
    'Verstaerker\I18nl10n\Classes\I18nl10nHooks'                   => 'system/modules/i18nl10n/classes/I18nl10nHooks.php',

    // Modules
    'Verstaerker\I18nl10n\Modules\ModuleI18nl10nLanguageSelection' => 'system/modules/i18nl10n/modules/ModuleI18nl10nLanguageSelection.php',

    // Pages
    'Verstaerker\I18nl10n\Pages\PageI18nl10nRegular'               => 'system/modules/i18nl10n/pages/PageI18nl10nRegular.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'lang_default'     => 'system/modules/i18nl10n/templates',
	'lang_select'      => 'system/modules/i18nl10n/templates',
	'mod_i18nl10n_nav' => 'system/modules/i18nl10n/templates',
	'nav_i18nl10n'     => 'system/modules/i18nl10n/templates',
	'nav_l10n'         => 'system/modules/i18nl10n/templates',
));
