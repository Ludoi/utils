<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ludoi\Logger;

class LoggerChannel {
    /** @var string */
    private $channel;
    
    /** @var string */
    private $folder;
    
    /** @var string */
    private $filename;
    
    public function __construct(string $channel, string $folder) {
	$this->channel = $channel;
	$this->folder = $folder;
	$this->filename = Nette/Utils/Strings::trim($this->folder) . Nette/Utils/Strings::trim($this->channel) . ".log";
    }
    
    public function getFilename( ): string {
	return $this->filename;
    }
    
    public function getChannel( ): string {
	return $this->channel;
    }
    
    public function addInfo(string $message) {
	$this->writeMessage('INFO', $message);	
    }
    
    public function addWarning(string $message) {
	$this->writeMessage('WARNING', $message);		
    }
    
    public function addError(string $message) {
	$this->writeMessage('ERROR', $message);		
    }    

    private function writeMessage(string $type, string $message) {
	$handle = fopen($this->filename, "w");
	$date = new Nette\Utils\DateTime( );
	$datestr = $date->__toString( );
	$logmessage = "$datestr $type $message \n";
	fwrite($handle, $logmessage);
	fclose($handle);
    }
}

