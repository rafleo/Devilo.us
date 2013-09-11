<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Line at rule element
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
namespace CSSTidy\Element;

class LineAt extends Element
{
    /** @var string */
    public $subValues;

    /** @var string */
    protected $value;

    /**
     * @param string $name
     * @param array $value
     */
    public function __construct($name, array $value)
    {
        $this->name = $name;
        $this->subValues = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        if (!isset($this->value)) {
            $this->value = ' ' . $this->mergeSubValues($this->subValues);
        }

        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "@{$this->getName()}{$this->getValue()};";
    }
}