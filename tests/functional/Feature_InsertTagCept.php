<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('check inserttags behaviour');


/*
 * Home
 */
// My location
$I->amOnPage('/');

// Inserttag state
$I->see('Standardseite', '.test-i18nl10n-inserttag');


/*
 * Home (ES)
 */
// Move to spanish version
$I->click('Español', '.i18nl10n_lang');

// Inserttag state
$I->see('Página por defecto', '.test-i18nl10n-inserttag');
