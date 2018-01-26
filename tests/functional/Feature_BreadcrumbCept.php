<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('check breadcrumb content element behaviour');


/*
 * Home
 */
// My location
$I->amOnPage('/');

// Breadcrumb state
$I->see('Home', '.mod_breadcrumb');


/*
 * Spezialseite (DE)
 */
// Move to "Spezialseite"
$I->click('Spezialseite');

// Language switcher states
$I->see('Deutsch', 'li > span');

// Breadcrumb state
$I->see('Spezialseite', '.mod_breadcrumb');


/*
 * Spezialseite (ES)
 */
// Move to spanish version
$I->click('Español', '.i18nl10n_lang');

// Language switcher states
$I->see('Deutsch', 'li > a > span');

// Breadcrumb state
$I->see('Página especial', '.mod_breadcrumb');
