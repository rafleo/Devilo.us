<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Comment element
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

class Property extends Element
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /** @var array */
    public $subValues = array();

    /** @var bool */
    public $isImportant = false;

    /**
     * @param string $name
     * @param array|string $value
     * @param int|null $line
     * @param bool $isImportant
     */
    public function __construct($name, $value, $line = null, $isImportant = false)
    {
        $this->name = $name;
        $this->line = (int) $line;
        $this->isImportant = (bool) $isImportant;

        if (is_array($value)) {
            $this->subValues = $value;
            $this->removeImportant();
        } else {
            $this->value = $value;
        }
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
        $this->subValues = array();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getValueWithoutImportant() . ($this->isImportant ? '!important' : '');
    }

    /**
     * @return string
     */
    public function getValueWithoutImportant()
    {
        if ($this->value === null) {
            $this->value = $this->mergeSubValues($this->subValues);
        }

        return $this->value;
    }

    /**
     * Check, if subValues contains !important.
     */
    protected function removeImportant()
    {
        foreach ($this->subValues as $key => $value) {
            if ($value{0} !== '!') {
                continue;
            }

            if (strcasecmp($value, '!important') === 0) {
                $this->isImportant = true;
                unset($this->subValues[$key]);
                break;
            } else if (isset($this->subValues[$key+1]) && strcasecmp($this->subValues[$key+1], 'important') === 0) {
                $this->isImportant = true;
                unset($this->subValues[$key], $this->subValues[$key+1]);
                break;
            }
        }
    }
}