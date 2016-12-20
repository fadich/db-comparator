<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Compare databases, for example "localhost@hookah" and "localhost@hookah" (with itself).');
$I->expectTo('Message about the identical of the databases.');
$I->amOnPage('?a=localhost%%root%%hookah&b=localhost%%root%%hookah');
$I->see('Comparison "localhost@hookah" and "localhost@hookah"');
$I->see('Databases are identical');
