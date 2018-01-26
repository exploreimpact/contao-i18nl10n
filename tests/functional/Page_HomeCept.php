<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('call the Home page and check its content');


/*
 * Home
 */
// My location
$I->amOnPage('/');
$I->see('Navigation', 'h1');

// Language switcher states
$I->see('Deutsch', 'li > span');
$I->see('English', 'li > a > span');
$I->see('Español', 'li > a > span');


/*
 * Home (EN)
 */
// Move to english version
$I->click('English', '.i18nl10n_lang');

// Language switcher states
$I->see('Deutsch', 'li > a > span');
$I->see('English', 'li > span');
$I->see('Español', 'li > a > span');


/*
 * Home (ES)
 */
// Move to spanish version
$I->click('Español', '.i18nl10n_lang');

// Language switcher states
$I->see('Deutsch', 'li > a > span');
$I->see('English', 'li > a > span');
$I->see('Español', 'li > span');
