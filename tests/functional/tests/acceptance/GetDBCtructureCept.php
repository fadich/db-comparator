<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->expectTo('Test DB comparator.');

$I->amOnPage('/');
$I->see('Please, enter params for database');

$I->wantTo('Get structure of database "localhost@gurps".');
$I->amOnPage('?a=localhost%%root%%gurps%%toor');
$I->see('Structure of "localhost@gurps"');
