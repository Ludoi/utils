<?php
declare(strict_types=1);
/*
   Copyright (C) 2020 Luděk Bednarz

   Project: utils
   Author:  Luděk Bednarz
*/

use Ludoi\Utils\ExtRef;
use Tester\Assert;

require __DIR__ . '/../../vendor/autoload.php';

Tester\Environment::setup();

Assert::same(ExtRef::getCheckNumber(1), 18);
Assert::true(ExtRef::isValid(18));
Assert::exception(function() { ExtRef::getCheckNumber(-1); } ,\Ludoi\Utils\Exception::class, 'Value is lower than 0');
Assert::same(ExtRef::compare(18, 17), 1);