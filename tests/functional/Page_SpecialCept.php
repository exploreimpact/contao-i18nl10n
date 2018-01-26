<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('move to the Special page and check its content');


/*
 * Home
 */
// My location
$I->amOnPage('/');


/*
 * Spezialseite
 */
// Move to "Spezialseite"
$I->click('Spezialseite');

// Check content
$I->see('German Special Content', 'h2');
$I->cantSee('English Special Content', 'h2');

// Language switcher states
$I->see('Deutsch', 'li > span');
$I->cantSee('English', 'li > a > span');
$I->see('Español', 'li > a > span');
