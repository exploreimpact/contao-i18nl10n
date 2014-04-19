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
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'I18nL10nHooks'       => 'system/modules/i18nl10n/classes/I18nL10nHooks.php',

	// Pages
	'I18nL10nPageRegular' => 'system/modules/i18nl10n/pages/I18nL10nPageRegular.php',
));
