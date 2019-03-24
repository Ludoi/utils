<?php

/* 
 * Copyright (C) 2019 Luděk
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http:www.gnu.org/licenses/>.
 */

require __DIR__ . '/../../../vendor/autoload.php';     

use Tester\Assert;
use Ludoi\Logger\Handler\SyslogHandler;
use Ludoi\Logger\Handler\FileHandler;

Tester\Environment::setup();

$syslogHandler = new SyslogHandler( );

Assert::same('SYSLOG', $syslogHandler->getHandlerType());

$fileHandler = new FileHandler('./logs/');

Assert::same('FILE', $fileHandler->getHandlerType());
Assert::same('./logs/users.log', $fileHandler->getFilename('USERS'));
