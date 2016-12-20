<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Compare databases, for example "localhost@gurps" and "localhost@gurps" (with itself).');
$I->expectTo('Message about the identical of the databases.');
$I->amOnPage('?a=localhost%%root%%gurps%%toor&b=localhost%%root%%gurps%%toor');
$I->see('Comparison "localhost@gurps" and "localhost@gurps"');
$I->see('Databases are identical');
