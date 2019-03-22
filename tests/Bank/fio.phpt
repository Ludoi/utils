<?php

use Tester\Assert;
use Ludoi\Bank\Fio;

# Načteme knihovny Testeru.
require __DIR__ . '/../../vendor/autoload.php';     

# Upraví chování PHP a zapne některé vlastnosti Testeru (popsáno dále)
Tester\Environment::setup();

Assert::true(new Fio('ABCD') instanceof Fio);