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

namespace Ludoi\Logger\Handler;

abstract class AbstractHandler
{
    /**
     * @var string
     */
    private string $handlerType;

    /**
     * AbstractHandler constructor.
     * @param string $handlerType
     */
    public function __construct(string $handlerType)
    {
        $this->handlerType = $handlerType;
    }

    /**
     * @return string
     */
    public function getHandlerType(): string
    {
        return $this->handlerType;
    }

    /**
     * @param int $priority
     * @param string $message
     * @param string $channel
     */
    public function writeMessage(int $priority, string $message, string $channel): void
    {

    }
}