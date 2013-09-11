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

abstract class Element
{
    /**
     * Number of line in file, which contains element in input CSS file
     * @var int
     */
    public $line;

    /**
     * @param array $subValues
     * @return string
     */
    protected function mergeSubValues(array $subValues)
    {
        $prev = false;
        $output = '';

        foreach ($subValues as $subValue) {
            if ($subValue === ',') {
                $prev = true;
            } else if (!$prev) {
                $output .= ' ';
            } else {
                $prev = false;
            }
            $output .= $subValue;
        }

        return ltrim($output, ' ');
    }
}