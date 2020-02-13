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

class SyslogHandler extends AbstractHandler {
    /**
     * SyslogHandler constructor.
     */
    public function __construct() {
        parent::__construct('SYSLOG');
    }

    /**
     * @param int $priority
     * @param string $message
     * @param string $channel
     */
    public function writeMessage(int $priority, string $message, string $channel): void {
        $this->open();
        $this->write($priority, $message, $channel);
        $this->close();
    }

    /**
     *
     */
    private function open( ): void {

    }

    /**
     * @param int $priority
     * @param string $message
     * @param string $channel
     */
    private function write(int $priority, string $message, string $channel): void {
        $log = "{Strings::trim($channel)} {Strings::trim($message)}";
        syslog($priority, $message);
    }

    /**
     *
     */
    private function close( ): void {

    }
}