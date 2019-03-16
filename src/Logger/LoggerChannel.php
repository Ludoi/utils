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
    
    public function __construct(string $channel) {
	$this->channel = $channel;
    }
    
    public function addInfo(string $message) {
	
    }
    
    public function addWarning(string $message) {
	
    }
    
    public function addError(string $message) {
	
    }    

    private function writeMessage(string $type, string $message) {
	
    }
}

