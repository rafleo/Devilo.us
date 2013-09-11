<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Selector block element
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

class Selector extends Block
{
    /** @var string */
    protected $name;

    /** @var array */
    public $subSelectors = array();

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     */
    public function appendSelectorName($name)
    {
        $this->subSelectors[] = $name;
        $this->name .= ',' . $name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Selector $selector
     * @return bool True, if $selector has same properties as $this
     */
    public function hasSameProperties(Selector $selector)
    {
        return $this->propertiesToArray($this) == $this->propertiesToArray($selector);
    }

    /**
     * Return Selector properties in format for comparing with other selector
     * @see hasSameProperties()
     * @param Selector $selector
     * @return array in format ['color:red', 'font-weight:bold]
     */
    protected function propertiesToArray(Selector $selector)
    {
        $output = array();
        foreach ($selector->elements as $element) {
            if ($element instanceof Property) {
                /** @var Property $element */
                $output[] = "{$element->getName()}:{$element->getValue()}";
            }
        }

        return $output;
    }
}