<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ludoi\Logger;

use Nette\Utils\Strings;

class LoggerChannel {
    /** @var string */
    private $channel;
    
    /** @var string */
    private $folder;
    
    /** @var string */
    private $filename;
    
    /** @var bool */
    private $syslog;

    public function __construct(string $channel, bool $syslog, string $folder) {
	$this->channel = $channel;
	$this->syslog = $syslog;
	if (!$this->syslog) {
	    $this->folder = $folder;
	    $this->filename = Strings::lower(Strings::trim($this->folder) . Strings::trim($this->channel) . ".log");
	}
    }
    
    public function getFilename( ): string {
	return $this->filename;
    }
    
    public function getChannel( ): string {
	return $this->channel;
    }
    
    public function getSyslog( ): bool {
	return $this->channel;
    }
    
    public function addInfo(string $message) {
	$this->writeMessage(LOG_INFO, $message);	
    }
    
    public function addWarning(string $message) {
	$this->writeMessage(LOG_WARNING, $message);		
    }
    
    public function addError(string $message) {
	$this->writeMessage(LOG_ERR, $message);		
    }    
    
    private function getType(int $priority):string {
	switch ($priority) {
	   case LOG_INFO:
	       return 'INFO';
	   case LOG_ERR:
	       return 'ERROR';
	   case LOG_WARNING:
	       return 'WARNING';
	}
    }

    private function writeMessage(int $priority, string $message) {
	$date = new Nette\Utils\DateTime( );
	$datestr = $date->__toString( );
	if ($this->syslog) {
	    syslog($priority, $message);
	} 
	else
	{
	    $logmessage = "$datestr {$this->getType($priority)} $message \n";
	    $handle = fopen($this->filename, "w");
	    fwrite($handle, $logmessage);
	    fclose($handle);
	}
    }
}

