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
    
    /** @var Ludoi\Logger\Logger */
    private $logger;
    
    public function __construct(string $channel, Logger $logger) {
	$this->channel = $channel;
	$this->logger = $logger;
//	if (!$this->syslog) {
//	    $this->folder = $folder;
//	    $this->filename = Strings::lower(Strings::trim($this->folder) . Strings::trim($this->channel) . ".log");
//	}
    }
    
//    public function getFilename( ): string {
//	return $this->filename;
//    }
    
    public function getChannel( ): string {
	return $this->channel;
    }
       
    public function addInfo(string $message) {
	$this->logger->getHandler( )->writeMessage(LOG_INFO, $message, $this->channel);	
    }
    
    public function addWarning(string $message) {
	$this->logger->getHandler( )->writeMessage(LOG_WARNING, $message, $this->channel);		
    }
    
    public function addError(string $message) {
	$this->logger->getHandler( )->writeMessage(LOG_ERR, $message, $this->channel);		
    }    
    

//    private function writeMessage(int $priority, string $message) {
//	$date = new Nette\Utils\DateTime( );
//	$datestr = $date->__toString( );
//	if ($this->syslog) {
//	    syslog($priority, $message);
//	} 
//	else
//	{
//	    $logmessage = "$datestr {$this->getType($priority)} $message \n";
//	    $handle = fopen($this->filename, "w");
//	    fwrite($handle, $logmessage);
//	    fclose($handle);
//	}
//    }
}

