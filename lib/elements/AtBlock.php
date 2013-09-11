<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * AtBlock element
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

class AtBlock extends Block
{
    /** @var array */
    public $nameParts = array();

    /**
     * @param string|array $name
     */
    public function __construct($name)
    {
        if (is_array($name)) {
            $this->nameParts = $name;
        } else {
            $this->name = $name;
        }
    }

    /**
     * @return string with @ character at begin
     */
    public function getName()
    {
        return '@' . $this->mergeSubValues($this->nameParts);
    }

    /**
     * @param Block $block
     * @return Block
     */
    public function addBlock(Block $block)
    {
        $name = '!' . $block->getName();

        while (isset($this->elements[$name])) {
            $name .= ' ';
        }

        return $this->elements[$name] = $block;
    }

    /**
     * @param Block $block
     * @return bool true if block was removed
     */
    public function removeBlock(Block $block)
    {
        $name = '!' . $block->getName();

        while (isset($this->elements[$name])) {
            if ($this->elements[$name] === $block) {
                unset($this->elements[$name]);
                return true;
            }
            $name .= ' ';
        }

        return false;
    }

    /**
     * @param Block $block
     * @return Block|bool
     */
    public function getBlockWithSameName(Block $block)
    {
        $name = '!' . $block->getName();

        while (isset($this->elements[$name])) {
            $sameBlock = $this->elements[$name];
            if (
                $sameBlock !== $block &&
                $sameBlock instanceof $block &&
                $sameBlock->getName() === $block->getName()
            ) {
                return $sameBlock;
            }
            $name .= ' ';
        }

        return false;
    }

    /**
     * @param Block $block
     */
    public function merge(Block $block)
    {
        foreach ($block->elements as $value) {
            if ($value instanceof Property) {
                $this->addProperty($value);
            } else if ($value instanceof Block) {
                $this->addBlock($value);
            } else if ($value instanceof LineAt) {
                $this->addLineAt($value);
            } else {
                throw new \Exception("Invalid element");
            }
        }
    }

    /**
     * @param LineAt $lineAt
     */
    public function addLineAt(LineAt $lineAt)
    {
        $this->elements[] = $lineAt;
    }
}