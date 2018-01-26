<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('move to the Sitemap page and check its content');

/*
 * Home
 */
// My location
$I->amOnPage('/');


/*
 * Sitemap
 */
// Move to "Sitemap"
$I->click('Sitemap');

// ... t.b.d. ...
