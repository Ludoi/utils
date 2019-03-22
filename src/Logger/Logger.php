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
    
    /** @var string */
    private $folder;
    
    public function channel(string $channel, bool $syslog = TRUE, string $folder = '') {
	if (!in_array($channel, $this->channels)) {
	    $this->channels[$channel] = new LoggerChannel($channel, $syslog, $folder);
	}
	return $this->channels[$channel];
    }
}
