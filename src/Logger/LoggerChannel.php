<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ludoi\Logger;

class LoggerChannel
{
    /** @var string */
    private $channel;

    /** @var Ludoi\Logger\Logger */
    private $logger;

    public function __construct(string $channel, Logger $logger)
    {
        $this->channel = $channel;
        $this->logger = $logger;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function addInfo(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_INFO, $message, $this->channel);
    }

    public function addWarning(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_WARNING, $message, $this->channel);
    }

    public function addError(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_ERR, $message, $this->channel);
    }

    public function addAlert(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_ALERT, $message, $this->channel);
    }
}

