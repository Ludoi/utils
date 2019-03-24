<?php

use Tester\Assert;
use Ludoi\Bank\Fio;

require __DIR__ . '/../../vendor/autoload.php';     

Tester\Environment::setup();

Assert::true(new Fio('ABCD') instanceof Fio);
