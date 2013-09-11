<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Abstract block element
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

abstract class Block extends Element
{
    /** @var \CSSTidy\Element\Element[] */
    public $elements = array();

    /**
     * @param Property $property
     */
    public function addProperty(Property $property)
    {
        $name = $property->getName();
        while (isset($this->elements[$name])) {
            $name .= ' ';
        }

        $this->elements[$name] = $property;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    /**
     * @param Property $property
     * @return bool
     */
    public function removeProperty(Property $property)
    {
        $name = $property->getName();
        while (isset($this->elements[$name])) {
            if ($this->elements[$name] === $property) {
                unset($this->elements[$name]);
                return true;
            }

            $name .= ' ';
        }

        return false;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
    {
        $this->elements[] = $comment;
    }

    /**
     * @param Elements[] $elements
     */
    public function mergeElements(array $elements)
    {
        foreach ($elements as $element) {
            if ($element instanceof Property) {
                $elementName = $element->getName();
                if (isset($this->elements[$elementName]) && $this->elements[$elementName] === false) {
                    $this->elements[$elementName] = $element;
                } else {
                    $this->addProperty($element);
                }
            } else {
                $this->elements[] = $element;
            }
        }
    }

    /**
     * @abstract
     * @return string
     */
    abstract public function getName();
}