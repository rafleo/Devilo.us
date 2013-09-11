<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Shorthand optimisation class
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
 * @author Florian Schmitz (floele at gmail dot com) 2005-2007
 * @author Brett Zamir (brettz9 at yahoo dot com) 2007
 * @author Nikolay Matsievsky (speed at webo dot name) 2009-2010
 * @author Cedric Morin (cedric at yterium dot com) 2010
 * @author Jakub Onderka (acci at acci dot cz) 2011
 */
namespace CSSTidy\Optimise;

use CSSTidy\Parser;
use CSSTidy\Logger;
use CSSTidy\Configuration;
use CSSTidy\Element;

class Shorthand
{
    /**
     * A list of all shorthand properties that are devided into four properties and/or have four subvalues
     *
     * @todo Are there new ones in CSS3?
     * @see dissolveFourValueShorthands()
     * @see mergeFourValueShorthands()
     * @var array
     */
    public static $shorthands = array(
        'border-color' => array('border-top-color','border-right-color','border-bottom-color','border-left-color'),
        'border-style' => array('border-top-style','border-right-style','border-bottom-style','border-left-style'),
        'border-width' => array('border-top-width','border-right-width','border-bottom-width','border-left-width'),
        'margin' => array('margin-top','margin-right','margin-bottom','margin-left'),
        'padding' => array('padding-top','padding-right','padding-bottom','padding-left'),
        'border-radius' => array('border-radius-top-left', 'border-radius-top-right', 'border-radius-bottom-right', 'border-radius-bottom-left')
    );

    /**
     * @static
     * @var array
     */
    public static $twoValuesShorthand = array(
        'overflow' => array('overflow-x', 'overflow-y'),
        'pause' => array('pause-before', 'pause-after'),
        'rest' => array('rest-before', 'rest-after'),
        'cue' => array('cue-before', 'cue-after'),
    );

    /**
     * Default values for the background properties
     *
     * @todo Possibly property names will change during CSS3 development
     * @see dissolveShortBackground()
     * @see merge_bg()
     * @var array
     */
    public static $backgroundPropDefault = array(
        'background-image' => 'none',
        'background-size' => 'auto',
        'background-repeat' => 'repeat',
        'background-position' => '0 0',
        'background-attachment' => 'scroll',
        'background-clip' => 'border',
        'background-origin' => 'padding',
        'background-color' => 'transparent'
    );

    /**
     * Default values for the font properties
     *
     * @see mergeFonts()
     * @var array
     */
    public static $fontPropDefault = array(
        'font-style' => 'normal',
        'font-variant' => 'normal',
        'font-weight' => 'normal',
        'font-size' => '',
        'line-height' => '',
        'font-family' => '',
    );

    /** @var int */
    protected $optimiseShorthand;

    /**
     * @param int $optimiseShorthand
     */
    public function __construct($optimiseShorthand)
    {
        $this->optimiseShorthand = $optimiseShorthand;
    }

    /**
     * @param \CSSTidy\Element\Block $block
     */
    public function process(Element\Block $block)
    {
        $this->dissolveShorthands($block);

        $this->mergeFourValueShorthands($block);
        $this->mergeTwoValuesShorthand($block);

        foreach (self::$shorthands as $shorthand => $foo) {
            if (isset($block->elements[$shorthand])) {
                $shorthand === 'border-radius' ?
                   $this->borderRadiusShorthand($block->elements[$shorthand])  :
                   $this->compressShorthand($block->elements[$shorthand]);
            }
        }

        if ($this->optimiseShorthand >= Configuration::FONT) {
            $this->mergeFont($block);

            if (isset($block->elements['font']) && $block->elements['font'] === false) {
                unset($block->elements['font']);
            }

            if ($this->optimiseShorthand >= Configuration::BACKGROUND) {
                $this->mergeBackground($block);

                if (isset($block->elements['background']) && $block->elements['background'] === false) {
                    unset($block->elements['background']);
                }

                if (empty($block->elements)) {
                    unset($block);
                }
            }
        }

        if (isset($block) && $block instanceof Element\AtBlock) {
            foreach ($block->elements as $value) {
                if ($value instanceof Element\Block) {
                    $this->process($value);
                }
            }
        }
    }

    /**
     * @param \CSSTidy\Element\Block $block
    */
    protected function dissolveShorthands(Element\Block $block)
    {
        if (isset($block->elements['font']) && $this->optimiseShorthand > Configuration::COMMON) {
            $property = $block->elements['font'];
            $block->elements['font'] = false;
            $block->mergeElements($this->dissolveShortFont($property));
        }

        if (isset($block->elements['background']) && $this->optimiseShorthand > Configuration::FONT) {
            $property = $block->elements['background'];
            $block->elements['background'] = false;
            $block->mergeElements($this->dissolveShortBackground($property));
        }
    }

    /**
     * Compresses shorthand values. Example: margin:1px 1px 1px 1px -> margin:1px
     * @param \CSSTidy\Element\Property $property
    */
    protected function compressShorthand(Element\Property $property)
    {
        $value = $property->getValueWithoutImportant();

        $values = Parser::explodeWithoutString(' ', $value);

        $property->setValue($this->compressShorthandValues($values));
    }


    /**
     * Optimize border-radius property
     *
     * @param \CSSTidy\Element\Property $property
     */
    protected function borderRadiusShorthand(Element\Property $property)
    {
        $value = $property->getValueWithoutImportant();
        $parts = explode('/', $value);

        if (empty($parts)) { // / delimiter in string not found
            return;
        }

        if (isset($parts[2])) {
            return; // border-radius value can contains only two parts
        }

        foreach ($parts as &$part) {
            $values = Parser::explodeWithoutString(' ', trim($part));
            $part = $this->compressShorthandValues($values);
        }

        $property->setValue(implode('/', $parts));
    }

    /**
     * @param array $values
     * @return string
     */
    protected function compressShorthandValues(array $values)
    {
        switch (count($values)) {
            case 4:
                if ($values[0] == $values[1] && $values[0] == $values[2] && $values[0] == $values[3]) {
                    return $values[0];
                } else if ($values[1] == $values[3] && $values[0] == $values[2]) {
                    return $values[0] . ' ' . $values[1];
                } else if ($values[1] == $values[3]) {
                    return $values[0] . ' ' . $values[1] . ' ' . $values[2];
                }
                break;

            case 3:
                if ($values[0] == $values[1] && $values[0] == $values[2]) {
                    return $values[0];
                } else if ($values[0] == $values[2]) {
                    return $values[0] . ' ' . $values[1];
                }
                break;

            case 2:
                if ($values[0] == $values[1]) {
                    return $values[0];
                }
                break;
        }

        return implode(' ', $values);
    }

    /**
     * Merges Shorthand properties again, the opposite of self::dissolveFourValueShorthands
     * @param \CSSTidy\Element\Block $block
     */
    protected function mergeFourValueShorthands(Element\Block $block)
    {
        foreach (self::$shorthands as $shorthand => $properties) {
            if (
                isset($block->elements[$properties[0]]) &&
                isset($block->elements[$properties[1]]) &&
                isset($block->elements[$properties[2]]) &&
                isset($block->elements[$properties[3]])
            ) {
                $important = false;
                $values = array();

                foreach ($properties as $property) {
                    /** @var \CSSTidy\Element\Property $prop */
                    $prop = $block->elements[$property];
                    if ($prop->isImportant) {
                        $important = true;
                    }
                    $values[] = $prop->getValueWithoutImportant();
                    $block->removeProperty($prop);
                }

                $shorthandProperty = new Element\Property($shorthand, $this->compressShorthandValues($values, $important));
                $shorthandProperty->isImportant = $important;
                $block->addProperty($shorthandProperty);
            }
        }
    }

    /**
     * Merge two values shorthand
     * Shorthand for merging are defined in self::$twoValuesShorthand
     * Example: overflow-x and overflow-y are merged to overflow shorthand
     * @param \CSSTidy\Element\Block $block
     * @see self::$twoValuesShorthand
     */
    protected function mergeTwoValuesShorthand(Element\Block $block)
    {
        foreach (self::$twoValuesShorthand as $shorthandProperty => $properties) {
            if (
                isset($block->elements[$properties[0]]) &&
                isset($block->elements[$properties[1]])
            ) {
                /** @var \CSSTidy\Element\Property $first */
                $first = $block->elements[$properties[0]];
                /** @var \CSSTidy\Element\Property $second */
                $second = $block->elements[$properties[1]];

                if ($first->isImportant !== $second->isImportant) {
                    continue;
                }

                $firstValue = $first->getValueWithoutImportant();
                $secondValue = $second->getValueWithoutImportant();

                if ($firstValue == $secondValue) {
                    $output = $firstValue;
                } else {
                    $output = "$firstValue $secondValue";
                }

                $property = new Element\Property($shorthandProperty, $output);
                $property->isImportant = $first->isImportant;
                $block->addProperty($property);

                $block->removeProperty($first);
                $block->removeProperty($second);
            }
        }
    }


    /**
     * Dissolve background property
     * @param \CSSTidy\Element\Property $property
     * @return array
     * @todo full CSS 3 compliance
    */
    protected function dissolveShortBackground(Element\Property $property)
    {
        $str_value = $property->getValueWithoutImportant();
        // don't try to explose background gradient !
        if (stripos($str_value, "gradient(") !== false) {
            return array(new Element\Property('background', $property->getValue()));
        }

        static $repeat = array('repeat', 'repeat-x', 'repeat-y', 'no-repeat', 'space');
        static $attachment = array('scroll', 'fixed', 'local');
        static $clip = array('border', 'padding');
        static $origin = array('border', 'padding', 'content');
        static $pos = array('top', 'center', 'bottom', 'left', 'right');

        $return = array(
            'background-image' => null,
            'background-size' => null,
            'background-repeat' => null,
            'background-position' => null,
            'background-attachment' => null,
            'background-clip' => null,
            'background-origin' => null,
            'background-color' => null
        );

        $str_value = Parser::explodeWithoutString(',', $str_value);
        foreach ($str_value as $strVal) {
            $have = array(
                'clip' => false,
                'pos' => false,
                'color' => false,
                'bg' => false,
            );

            if (is_array($strVal)) {
                $strVal = $strVal[0];
            }

            $strVal = Parser::explodeWithoutString(' ', trim($strVal));

            foreach ($strVal as $current) {
                if ($have['bg'] === false && (substr($current, 0, 4) === 'url(' || $current === 'none')) {
                    $return['background-image'] .= $current . ',';
                    $have['bg'] = true;
                } else if (in_array($current, $repeat, true)) {
                    $return['background-repeat'] .= $current . ',';
                } else if (in_array($current, $attachment, true)) {
                    $return['background-attachment'] .= $current . ',';
                } else if (in_array($current, $clip, true) && !$have['clip']) {
                    $return['background-clip'] .= $current . ',';
                    $have['clip'] = true;
                } else if (in_array($current, $origin, true)) {
                    $return['background-origin'] .= $current . ',';
                } else if ($current{0} === '(') {
                    $return['background-size'] .= substr($current, 1, -1) . ',';
                } else if (in_array($current, $pos, true) || is_numeric($current{0}) || $current{0} === null || $current{0} === '-' || $current{0} === '.') {
                    $return['background-position'] .= $current . ($have['pos'] ? ',' : ' ');
                    $have['pos'] = true;
                } else if (!$have['color']) {
                    $return['background-color'] .= $current . ',';
                    $have['color'] = true;
                }
            }
        }

        $outputProperties = array();
        foreach (self::$backgroundPropDefault as $backgroundProperty => $defaultValue) {
            if (isset($return[$backgroundProperty])) {
                $outputProperties[] = new Element\Property($backgroundProperty, substr($return[$backgroundProperty], 0, -1), 0, $property->isImportant);
            } else {
                $outputProperties[] = new Element\Property($backgroundProperty, $defaultValue, 0, $property->isImportant);
            }
        }

        return $outputProperties;
    }

    /**
     * Merges all background properties
     * @param \CSSTidy\Element\Block $block
     * @todo full CSS 3 compliance
     */
    protected function mergeBackground(Element\Block $block)
    {
        $properties = $block->elements;

        // if background properties is here and not empty, don't try anything
        if (isset($properties['background']) && $properties['background']) {
            return;
        }

        // Array with background images to check if BG image exists
        $explodedImage = isset($properties['background-image']) ?
            Parser::explodeWithoutString(',', $block->elements['background-image']->getValueWithoutImportant()) : array();

        $colorCount = isset($properties['background-color']) ?
            count(Parser::explodeWithoutString(',', $block->elements['background-color']->getValueWithoutImportant())) : 0;

        // Max number of background images. CSS3 not yet fully implemented
        $numberOfValues = max(count($explodedImage), $colorCount, 1);

        $newBackgroundValue = '';
        $important = '';

        for ($i = 0; $i < $numberOfValues; $i++) {
            foreach (self::$backgroundPropDefault as $property => $defaultValue) {
                // Skip if property does not exist
                if (!isset($block->elements[$property])) {
                    continue;
                }

                $property = $block->elements[$property];
                $currentValue = $property->getValueWithoutImportant();
                // skip all optimisation if gradient() somewhere
                if (stripos($currentValue, "gradient(") !== false) {
                    return;
                }

                // Skip some properties if there is no background image
                if ((!isset($explodedImage[$i]) || $explodedImage[$i] === 'none')
                                && ($property === 'background-size' || $property === 'background-position'
                                || $property === 'background-attachment' || $property === 'background-repeat')) {
                    continue;
                }

                if ($property->isImportant) {
                    $important = true;
                }

                // Do not add default values
                if ($currentValue === $defaultValue) {
                    continue;
                }

                $temp = Parser::explodeWithoutString(',', $currentValue);

                if (isset($temp[$i])) {
                    if ($property === 'background-size') {
                        $newBackgroundValue .= '(' . $temp[$i] . ') ';
                    } else {
                        $newBackgroundValue .= $temp[$i] . ' ';
                    }
                }
            }

            $newBackgroundValue = trim($newBackgroundValue);
            if ($i != $numberOfValues - 1) {
                $newBackgroundValue .= ',';
            }
        }

        // Delete all background-properties
        foreach (self::$backgroundPropDefault as $property => $foo) {
            if (isset($properties[$property])) {
                $block->removeProperty($properties[$property]);
            }
        }

        // Add new background property
        if ($newBackgroundValue !== '') {
            $newProperty = new Element\Property('background', $newBackgroundValue, 0, $important);
        } else if (isset($block->elements['background'])) {
            $newProperty = new Element\Property('background', 'none');
        }

        if (isset($newProperty)) {
            $block->mergeElements(array($newProperty));
        }
    }

    /**
     * Dissolve font property
     * @param \CSSTidy\Element\Property $property
     * @return array
    */
    protected function dissolveShortFont(Element\Property $property)
    {
        static $fontWeight = array('normal', 'bold', 'bolder', 'lighter', 100, 200, 300, 400, 500, 600, 700, 800, 900);
        static $fontVariant = array('normal', 'small-caps');
        static $fontStyle = array('normal', 'italic', 'oblique');

        $value = $property->getValueWithoutImportant();

        $return = array(
            'font-style' => null,
            'font-variant' => null,
            'font-weight' => null,
            'font-size' => null,
            'line-height' => null,
            'font-family' => null
        );

        $have = array(
            'style' => false,
            'variant' => false,
            'weight' => false,
            'size' => false,
        );

        // Workaround with multiple font-family
        $value = Parser::explodeWithoutString(',', trim($value));

        $beforeColon = array_shift($value);
        $beforeColon = Parser::explodeWithoutString(' ', trim($beforeColon));

        foreach ($beforeColon as $propertyValue) {
            if ($have['weight'] === false && in_array($propertyValue, $fontWeight, true)) {
                $return['font-weight'] = $propertyValue;
                $have['weight'] = true;
            } else if ($have['variant'] === false && in_array($propertyValue, $fontVariant)) {
                $return['font-variant'] = $propertyValue;
                $have['variant'] = true;
            } else if ($have['style'] === false && in_array($propertyValue, $fontStyle)) {
                $return['font-style'] = $propertyValue;
                $have['style'] = true;
            } else if ($have['size'] === false && (is_numeric($propertyValue{0}) || $propertyValue{0} === null || $propertyValue{0} === '.')) {
                $size = Parser::explodeWithoutString('/', trim($propertyValue));
                $return['font-size'] = $size[0];
                $return['line-height'] = isset($size[1]) ? $size[1] : '';
                $have['size'] = true;
            } else {
                $return['font-family'] .= isset($return['font-family']) ? ' ' . $propertyValue : $propertyValue;
            }
        }

        foreach ($value as $fontFamily) {
            $return['font-family'] .= ',' . trim($fontFamily);
        }

        // Fix for 100 and more font-size
        if ($have['size'] === false && isset($return['font-weight']) && is_numeric($return['font-weight']{0})) {
            $return['font-size'] = $return['font-weight'];
            unset($return['font-weight']);
        }

        $outputProperties = array();
        foreach (self::$fontPropDefault as $fontProperty => $defaultValue) {
            if (isset($return[$fontProperty]) && !empty($return[$fontProperty])) {
                $outputProperties[] = new Element\Property($fontProperty, $return[$fontProperty], 0, $property->isImportant);
            }
        }

        return $outputProperties;
    }

    /**
     * Merge font properties into font shorthand
     * @todo: refactor
     * @param \CSSTidy\Element\Block $block
     */
    protected function mergeFont(Element\Block $block)
    {
        $newFontValue = '';
        $preserveFontVariant = $important = false;

        // Skip if is font-size and font-family not set
        if (isset($block->elements['font-size']) && isset($block->elements['font-family'])) {
            foreach (self::$fontPropDefault as $fontProperty => $defaultValue) {

                // Skip if property does not exist
                if (!isset($block->elements[$fontProperty])) {
                    continue;
                }
                /** @var \CSSTidy\Property $property */
                $property = $block->elements[$fontProperty];

                $currentValue = $property->getValueWithoutImportant();

                /**
                 * Skip if default value is used or if font-variant property is not small-caps
                 * Stop if font-family contains inherit, because is not valid inside font shorthand
                 * @see http://www.w3.org/TR/css3-fonts/#propdef-font
                */
                if ($currentValue === $defaultValue) {
                    continue;
                } else if ($fontProperty === 'font-variant' && $currentValue !== 'small-caps') {
                    $preserveFontVariant = true;
                    continue;
                } else if ($fontProperty === 'font-family' && $currentValue === 'inherit') {
                    return;
                }

                if ($property->isImportant) {
                    $important = true;
                }

                $newFontValue .= $currentValue;

                if ($fontProperty === 'font-size' &&
                    isset($block->elements['line-height']) &&
                    $block->elements['line-height'] !== ''
                ) {
                    $newFontValue .= '/';
                } else {
                    $newFontValue .= ' ';
                }
            }

            $newFontValue = trim($newFontValue);

            if ($newFontValue !== '') {
                // Delete all font-properties
                foreach (self::$fontPropDefault as $fontProperty => $defaultValue) {
                    if (isset($block->elements[$fontProperty]) && !($fontProperty === 'font-variant' && $preserveFontVariant) && $fontProperty !== 'font') {
                        $block->removeProperty($block->elements[$fontProperty]);
                    }
                }

                // Add new font property
                $property = new Element\Property('font', $newFontValue, 0, $important);
                $block->mergeElements(array($property));
            }
        }
    }
}