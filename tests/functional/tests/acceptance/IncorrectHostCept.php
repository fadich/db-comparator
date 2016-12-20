<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Test connection with incorrect DB-host.');
$I->expectTo('Message such as "Unknown user "Error connection to host \'some_host\'".');
$I->amOnPage('?a=some_host%%root%%gurps');
$I->see('Error connection to host \'some_host\'');
