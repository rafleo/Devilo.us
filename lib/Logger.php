<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Logger
 *
 * Copyright 2005, 2006, 2007 Florian Schmitz
 *
 * This file is part of CSSTidy.
 *
 *   CSSTidy is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation; either version 2.1 of the License, or
 *   (at your option) any later version.
 *
 *   CSSTidy is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package CSSTidy
 * @author Jakub Onderka (acci at acci dot cz) 2011
 */
namespace CSSTidy;

class Logger
{
    const MESSAGE = 'm',
        TYPE = 't';

    // Constants for log type
    const ERROR = 'Error',
        WARNING = 'Warning',
        INFORMATION = 'Information';

    /** @var array */
    protected $log = array();

    /**
     * Add a message to the message log
     * @param string $message
     * @param string $type
     * @param integer $line
     */
    public function log($message, $type, $line = ' ')
    {
        $add = array(self::MESSAGE => $message, self::TYPE => $type);
        if (!isset($this->log[$line]) || !in_array($add, $this->log[$line])) {
            $this->log[$line][] = $add;
        }
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->log;
    }
}