<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ludoi\Logger;

class Logger {
    /** @var array */
    private $channels = array();
    
    private $handler;
    
    public function __construct(Handler\AbstractHandler $handler) {
	$this->handler = $handler;
    }
    
    public function getHandler( ): Handler\AbstractHandler {
	return $this->handler;
    }
    
    public function getChannel(string $channel): LoggerChannel {
	if (!in_array($channel, $this->channels)) {
	    $this->channels[$channel] = new LoggerChannel($channel, $this);
	}
	return $this->channels[$channel];
    }
}
