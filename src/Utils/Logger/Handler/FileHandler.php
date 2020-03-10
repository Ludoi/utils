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

namespace Ludoi\Utils\Logger\Handler;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;

class FileHandler extends AbstractHandler
{
    /**
     * @var string
     */
    private string $folder;

    /**
     * @var resource
     */
    private $fileHandler;

    /**
     * @var array
     */
    private array $priorities = array(
        LOG_INFO => 'INFO',
        LOG_ERR => 'ERROR',
        LOG_WARNING => 'WARNING',
        LOG_ALERT => 'ALERT'
    );

    /**
     * FileHandler constructor.
     * @param string $folder
     */
    public function __construct(string $folder)
    {
        parent::__construct('FILE');
        $this->folder = Strings::trim($folder);
    }

    /**
     * @param string $channel
     * @return string
     */
    public function getFilename(string $channel): string
    {
        return $this->folder . Strings::lower(Strings::trim($channel) . ".log");
    }

    /**
     * @param string $channel
     */
    private function openFile(string $channel) {
        $this->fileHandler = fopen($this->getFilename($channel), 'a');
    }

    /**
     *
     */
    private function closeFile() {
        fclose($this->fileHandler);
    }

    /**
     * @param int $priority
     * @param string $message
     * @param string $channel
     * @throws \Exception
     */
    public function writeMessage(int $priority, string $message, string $channel): void
    {
        $this->openFile($channel);
        $now = new DateTime();
        $messageText = Strings::trim($message);
        $log = "{$now->__toString()} {$this->priorities[$priority]} {$messageText}\n";
        fwrite($this->fileHandler, $log);
        $this->closeFile();
    }
}