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

namespace Ludoi\Utils\Timer;


class Timer
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var float
     */
    private float $startTime;
    /**
     * @var float
     */
    private float $endTime;

    /**
     * Timer constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     *
     */
    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    /**
     *
     */
    public function stop(): void {
        $this->endTime = microtime(true);
    }

    /**
     * @return float
     */
    public function duration(): float {
        return ($this->endTime - $this->startTime);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}