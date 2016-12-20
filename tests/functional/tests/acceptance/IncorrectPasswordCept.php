<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Test connection with incorrect password.');
$I->expectTo('Message such as "access denied".');
$I->amOnPage('?a=localhost%%root%%hookah%%qwe');
$I->see('Access denied for user \'root\'@\'localhost\' (using password: YES)');
