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

namespace Ludoi\Utils\Logger;

use Ludoi\Utils\Logger\Handler\AbstractHandler;

class Logger
{
    /** @var array */
    private array $channels = array();

    /**
     * @var AbstractHandler
     */
    private AbstractHandler $handler;

    /**
     * Logger constructor.
     * @param AbstractHandler $handler
     */
    public function __construct(AbstractHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return AbstractHandler
     */
    public function getHandler(): AbstractHandler
    {
        return $this->handler;
    }

    /**
     * @param string $channel
     * @return LoggerChannel
     */
    public function getChannel(string $channel): LoggerChannel
    {
        if (!in_array($channel, $this->channels)) {
            $this->channels[$channel] = new LoggerChannel($channel, $this);
        }
        return $this->channels[$channel];
    }
}
