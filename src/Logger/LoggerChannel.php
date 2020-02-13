<?php
declare(strict_types=1);
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


namespace Ludoi\Logger;

class LoggerChannel
{
    /**
     * @var string
     */
    private string $channel;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * LoggerChannel constructor.
     * @param string $channel
     * @param Logger $logger
     */
    public function __construct(string $channel, Logger $logger)
    {
        $this->channel = $channel;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param string $message
     */
    public function addInfo(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_INFO, $message, $this->channel);
    }

    /**
     * @param string $message
     */
    public function addWarning(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_WARNING, $message, $this->channel);
    }

    /**
     * @param string $message
     */
    public function addError(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_ERR, $message, $this->channel);
    }

    /**
     * @param string $message
     */
    public function addAlert(string $message)
    {
        $this->logger->getHandler()->writeMessage(LOG_ALERT, $message, $this->channel);
    }
}

