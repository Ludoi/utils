<?php

use Tester\Assert;
use Ludoi\Logger\Logger;

# Načteme knihovny Testeru.
require __DIR__ . '/../../vendor/autoload.php';     

# Upraví chování PHP a zapne některé vlastnosti Testeru (popsáno dále)
Tester\Environment::setup();


$logger = new Logger;

Assert::true($logger->channel('TEST') instanceof Ludoi\Logger\LoggerChannel, 'Correct class'); 

Assert::same($logger->channel('TEST2')->getChannel( ), 'TEST2', 'Correct channel'); 

Assert::true($logger->channel('TEST4')->getSyslog( ), 'Correct syslog'); 

Assert::same($logger->channel('TEST3', FALSE, 'FOLDER/')->getFilename(), "folder/test3.log", 'Correct folder');
