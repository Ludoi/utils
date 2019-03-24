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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Ludoi\Logger\Handler;

use Nette\Utils\Strings;

class FileHandler extends AbstractHandler {
    private $folder;
    
    private $fileHandler = NULL;
    
    private $priorities = [ 
	LOG_INFO => 'INFO',
	LOG_ERR => 'ERROR',
	LOG_WARNING => 'WARNING'
    ];    
    
    public function __construct(string $folder) {
	parent::__construct('FILE');
	$this->folder = Strings::trim($folder);
    }
    
    public function getFilename(string $channel): string {
	return $this->folder . Strings::lower(Strings::trim($channel) . ".log");
    }
    
    public function writeMessage(int $priority, string $message, string $channel): void {
	if (is_null($this->fileHandler)) {
	    $this->fileHandler = fopen($this->getFilename($channel), 'w');
	}
	$now = new \Nette\Utils\DateTime( );
	$log = "{$now->__toString()} {$this->priorities[$priority]} {Strings::trim($message)} \n";
	fwrite($log);
    }    
}