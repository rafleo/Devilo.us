<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Class for manipulating with parsed CSS
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

class SelectorManipulate
{
    /**
     * Merge selectors or at blocks with same name
     * Example: a {color:red} a {font-weight:bold} -> a {color:red;font-weight:bold}
     * @param AtBlock $block
     */
    public function mergeWithSameName(Element\AtBlock $block)
    {
        // Because elements are removed from $block->properties, foreach cannot be used
        reset($block->elements);
        while ($element = current($block->elements)) {
            next($block->elements);
            if (
                !$element instanceof Element\Block ||
                ($element instanceof Element\AtBlock && $element->getName() === '@font-face') // never merge @font-face
            ) {
                continue;
            }

            /** @var Block $element */
            $sameBlock = $block->getBlockWithSameName($element);
            if ($sameBlock) {
                if ($element instanceof Element\AtBlock) {
                    /** @var Element\AtBlock $element */
                    $element->merge($sameBlock);
                } else {
                    $element->mergeElements($sameBlock->elements);
                }
                $block->removeBlock($sameBlock);
            }

            if ($element instanceof Element\AtBlock) {
                $this->mergeWithSameName($element);
            }
        }
    }

    /**
     * Merge selector with same properties
     * Example: a {color:red} b {color:red} -> a,b {color:red}
     * @param Element\AtBlock $block
     */
    public function mergeWithSameProperties(Element\AtBlock $block)
    {
        // Because elements are removed from $block->properties, foreach cannot be used
        reset($block->elements);
        while (($element = current($block->elements))) {
            next($block->elements);
            if (!$element instanceof Element\Block) {
                continue;
            } else if (!$element instanceof Element\Selector) {
                $this->mergeWithSameProperties($element);
                continue;
            }

            $sameSelectors = array();
            foreach ($block->elements as $val) {
                if (!$val instanceof Element\Selector) {
                    continue;
                }

                if ($val !== $element && $element->hasSameProperties($val)) {
                    $sameSelectors[] = $val;
                }
            }

            if (!empty($sameSelectors)) {
                foreach ($sameSelectors as $sameSelector) {
                    /** @var Element\Selector $element */
                    $element->appendSelectorName($sameSelector->getName());
                    $block->removeBlock($sameSelector);
                }
            }
        }
    }

    /**
     * Removes invalid selectors and their corresponding rule-sets as
     * defined by 4.1.7 in REC-CSS2. This is a very rudimentary check
     * and should be replaced by a full-blown parsing algorithm or
     * regular expression
     * @param Element\AtBlock $block
     */
    public function discardInvalid(Element\AtBlock $block)
    {
        foreach ($block->elements as $key => $selector) {
            if ($selector instanceof Element\AtBlock) {
                $this->discardInvalid($selector);
                continue;
            } else if (!$selector instanceof Element\Block) {
                continue;
            }

            $ok = true;
            $selectors = array_map('trim', explode(',', $selector->getName()));

            foreach ($selectors as $s) {
                $simpleSelectors = preg_split('/\s*[+>~\s]\s*/', $s);
                foreach ($simpleSelectors as $ss) {
                    if ($ss === '') {
                        $ok = false;
                        break 2;
                    }
                    // could also check $ss for internal structure,
                    // but that probably would be too slow
                }
            }

            if (!$ok) {
                unset($block->elements[$key]);
            }
        }
    }

    /**
     * Separate selector for better reability
     * Example: a,b {color:red} -> b {color:red} b {color:red}
     * @param Element\AtBlock $block
     */
    public function separate(Element\AtBlock $block)
    {
        foreach ($block->elements as $element) {
            if (!$element instanceof Element\Block) {
                continue;
            } else if ($element instanceof Element\AtBlock) {
                $this->separate($element);
                continue;
            }

            /** @var Element\Selector $element */
            if (count($element->subSelectors) <= 1) {
                continue;
            }

            foreach ($element->subSelectors as $subSelector) {
                $newSelector = new Element\Selector($subSelector);
                $newSelector->elements = $element->elements;
                $block->addBlock($newSelector);
            }

            $block->removeBlock($element);
        }
    }
}