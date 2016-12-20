<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Test connection with incorrect username.');
$I->expectTo('Access denied for user \'root\'@\'localhost\' (using password: NO).');
$I->amOnPage('?a=localhost%%some_incorrect_name%%gurps');
$I->see('Access denied for user \'some_incorrect_name\'@\'localhost\' (using password: NO)');
