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
    
    public function channel(string $channel) {
	if (!in_array($channel, $this->channels)) {
	    $this->channels[$channel] = new LoggerChannel($channel);
	}
	return $this->channels[$channel];
    }
}
