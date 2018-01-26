<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('move to the Standard page and check its content');


/*
 * Home
 */
// My location
$I->amOnPage('/');


/*
 * Standardseite
 */
// Move to "Standardseite"
$I->click('Standardseite');

// Check content
$I->see('German Standard Content', 'h2');
$I->cantSee('English Standard Content', 'h2');

// Language switcher states
$I->see('Deutsch', 'li > span');
$I->see('English', 'li > a > span');
$I->see('Español', 'li > a > span');


/*
 * Standardseite (EN)
 */
// Change language to "English"
$I->click('English', '.i18nl10n_lang_en');

// Language switcher states
$I->see('Deutsch', 'li > a > span');
$I->see('English', 'li > span');
$I->see('Español', 'li > a > span');
