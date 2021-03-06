<?php

require __DIR__ . '/../../vendor/autoload.php';

use Ludoi\Logger\Handler\AbstractHandler;
use Tester\Assert;
use Ludoi\Logger\Handler\FileHandler;
use Ludoi\Logger\LoggerChannel;
use Ludoi\Logger\Logger;
use Tester\Environment;

Environment::setup();

$fileHandler = new FileHandler('./logs/');

$logger = new Logger($fileHandler);

Assert::true($logger->getChannel('TEST') instanceof LoggerChannel, 'Correct class'); 

Assert::same($logger->getChannel('TEST2')->getChannel( ), 'TEST2', 'Correct channel'); 

Assert::true($logger->getHandler() instanceof AbstractHandler, 'Correct handler');
