<?php
declare(strict_types=1);
/*
   Copyright (C) 2020 Luděk Bednarz

   Project: utils
   Author:  Luděk Bednarz
*/

use Tester\Assert;
use Ludoi\ExtRef;

require __DIR__ . '/../../vendor/autoload.php';

Tester\Environment::setup();

Assert::same(ExtRef::getCheckNumber(1), 5);
