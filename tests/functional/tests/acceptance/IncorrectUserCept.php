<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Test connection with incorrect username.');
$I->expectTo('Message such as "Unknown user \'some_incorrect_name\'@\'localhost\'".');
$I->amOnPage('?a=localhost%%some_incorrect_name%%hookah');
$I->see('Unknown user \'some_incorrect_name\'@\'localhost\'');
