<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Color optimisation class
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

use CSSTidy\Logger;

class Color
{
    /**
     * A list of non-W3C color names which get replaced by their hex-codes
     *
     * @see http://www.w3.org/TR/css3-color/#svg-color
     * @see optimise()
     * @var array
     */
    public static $replaceColors = array(
        'aliceblue' => '#f0f8ff',
        'antiquewhite' => '#faebd7',
        'aquamarine' => '#7fffd4',
        'azure' => '#f0ffff',
        'beige' => '#f5f5dc',
        'bisque' => '#ffe4c4',
        'blanchedalmond' => '#ffebcd',
        'blueviolet' => '#8a2be2',
        'brown' => '#a52a2a',
        'burlywood' => '#deb887',
        'cadetblue' => '#5f9ea0',
        'chartreuse' => '#7fff00',
        'chocolate' => '#d2691e',
        'coral' => '#ff7f50',
        'cornflowerblue' => '#6495ed',
        'cornsilk' => '#fff8dc',
        'crimson' => '#dc143c',
        'cyan' => '#00ffff',
        'darkblue' => '#00008b',
        'darkcyan' => '#008b8b',
        'darkgoldenrod' => '#b8860b',
        'darkgray' => '#a9a9a9',
        'darkgreen' => '#006400',
        'darkkhaki' => '#bdb76b',
        'darkmagenta' => '#8b008b',
        'darkolivegreen' => '#556b2f',
        'darkorange' => '#ff8c00',
        'darkorchid' => '#9932cc',
        'darkred' => '#8b0000',
        'darksalmon' => '#e9967a',
        'darkseagreen' => '#8fbc8f',
        'darkslateblue' => '#483d8b',
        'darkslategray' => '#2f4f4f',
        'darkturquoise' => '#00ced1',
        'darkviolet' => '#9400d3',
        'deeppink' => '#ff1493',
        'deepskyblue' => '#00bfff',
        'dimgray' => '#696969',
        'dodgerblue' => '#1e90ff',
        'feldspar' => '#d19275',
        'firebrick' => '#b22222',
        'floralwhite' => '#fffaf0',
        'forestgreen' => '#228b22',
        'gainsboro' => '#dcdcdc',
        'ghostwhite' => '#f8f8ff',
        'gold' => '#ffd700',
        'goldenrod' => '#daa520',
        'greenyellow' => '#adff2f',
        'honeydew' => '#f0fff0',
        'hotpink' => '#ff69b4',
        'indianred' => '#cd5c5c',
        'indigo' => '#4b0082',
        'ivory' => '#fffff0',
        'khaki' => '#f0e68c',
        'lavender' => '#e6e6fa',
        'lavenderblush' => '#fff0f5',
        'lawngreen' => '#7cfc00',
        'lemonchiffon' => '#fffacd',
        'lightblue' => '#add8e6',
        'lightcoral' => '#f08080',
        'lightcyan' => '#e0ffff',
        'lightgoldenrodyellow' => '#fafad2',
        'lightgrey' => '#d3d3d3',
        'lightgreen' => '#90ee90',
        'lightpink' => '#ffb6c1',
        'lightsalmon' => '#ffa07a',
        'lightseagreen' => '#20b2aa',
        'lightskyblue' => '#87cefa',
        'lightslateblue' => '#8470ff',
        'lightslategray' => '#778899',
        'lightsteelblue' => '#b0c4de',
        'lightyellow' => '#ffffe0',
        'limegreen' => '#32cd32',
        'linen' => '#faf0e6',
        'magenta' => '#ff00ff',
        'mediumaquamarine' => '#66cdaa',
        'mediumblue' => '#0000cd',
        'mediumorchid' => '#ba55d3',
        'mediumpurple' => '#9370d8',
        'mediumseagreen' => '#3cb371',
        'mediumslateblue' => '#7b68ee',
        'mediumspringgreen' => '#00fa9a',
        'mediumturquoise' => '#48d1cc',
        'mediumvioletred' => '#c71585',
        'midnightblue' => '#191970',
        'mintcream' => '#f5fffa',
        'mistyrose' => '#ffe4e1',
        'moccasin' => '#ffe4b5',
        'navajowhite' => '#ffdead',
        'oldlace' => '#fdf5e6',
        'olivedrab' => '#6b8e23',
        'orangered' => '#ff4500',
        'orchid' => '#da70d6',
        'palegoldenrod' => '#eee8aa',
        'palegreen' => '#98fb98',
        'paleturquoise' => '#afeeee',
        'palevioletred' => '#d87093',
        'papayawhip' => '#ffefd5',
        'peachpuff' => '#ffdab9',
        'peru' => '#cd853f',
        'pink' => '#ffc0cb',
        'plum' => '#dda0dd',
        'powderblue' => '#b0e0e6',
        'rosybrown' => '#bc8f8f',
        'royalblue' => '#4169e1',
        'saddlebrown' => '#8b4513',
        'salmon' => '#fa8072',
        'sandybrown' => '#f4a460',
        'seagreen' => '#2e8b57',
        'seashell' => '#fff5ee',
        'sienna' => '#a0522d',
        'skyblue' => '#87ceeb',
        'slateblue' => '#6a5acd',
        'slategray' => '#708090',
        'snow' => '#fffafa',
        'springgreen' => '#00ff7f',
        'steelblue' => '#4682b4',
        'tan' => '#d2b48c',
        'thistle' => '#d8bfd8',
        'tomato' => '#ff6347',
        'turquoise' => '#40e0d0',
        'violet' => '#ee82ee',
        'violetred' => '#d02090',
        'wheat' => '#f5deb3',
        'whitesmoke' => '#f5f5f5',
        'yellowgreen' => '#9acd32'
    );

    /** @var \CSSTidy\Logger */
    protected $logger;

    /** @var \CSSTidy\Optimise\Number */
    protected $optimiseNumber;

    public function __construct(Logger $logger, Number $optimiseNumber)
    {
        $this->logger = $logger;
        $this->optimiseNumber = $optimiseNumber;
    }

    /**
     * Color compression function. Converts all rgb() or hsl() values to #-values and uses the short-form if possible.
     * Also replaces 4 color names by #-values.
     * @param string $color
     * @return string
     */
    public function optimise($color)
    {
        static $shorterNames = array(
            /* color name -> hex code */
            'black' => '#000',
            'fuchsia' => '#f0f',
            'white' => '#fff',
            'yellow' => '#ff0',

            /* hex code -> color name */
            '#800000' => 'maroon',
            '#ffa500' => 'orange',
            '#808000' => 'olive',
            '#800080' => 'purple',
            '#008000' => 'green',
            '#000080' => 'navy',
            '#008080' => 'teal',
            '#c0c0c0' => 'silver',
            '#808080' => 'gray',
            '#f00' => 'red',
        );

        $original = $color;

        // rgb(0,0,0) -> #000000 (or #000 in this case later)
        $type = strtolower(strstr($color, '(', true));

        if ($type === 'rgb' || $type === 'hsl' ) {
            $colorTmp = substr($color, 4, -1);
            $colorTmp = explode(',', $colorTmp);

            if (count($colorTmp) > 3) {
                $this->logger->log("RGB or HSL color value supports only three items", Logger::WARNING);
                $colorTmp = array_slice($colorTmp, 0, 3);
            } else if (count($colorTmp) !== 3) {
                $this->logger->log("RGB or HSL color value supports only three items", Logger::ERROR);
                return $color;
            }

            if ($type === 'rgb') {
                $color = $this->convertRgbToHex($colorTmp);
            } else {
                $color = $this->convertHslToHex($colorTmp);
            }
        } else if ($type === 'rgba' || $type === 'hsla') {
            $colorTmp = substr($color, 5, -1);
            $colorTmp = explode(',', $colorTmp);

            if (count($colorTmp) > 4) {
                $this->logger->log(strtoupper($type) . " color value supports only four items", Logger::WARNING);
                $colorTmp = array_slice($colorTmp, 0, 4);
            } else if (count($colorTmp) !== 4) {
                $this->logger->log(strtoupper($type) ." color value supports only four items", Logger::ERROR);
                return $color;
            }

            if ($colorTmp[3] == 1) { // no alpha is set -> convert to HEX
                $colorTmp = array_slice($colorTmp, 0, 3);
                $color = ($type === 'rgba' ? $this->convertRgbToHex($colorTmp) : $this->convertHslToHex($colorTmp));
            } else if ($colorTmp[3] == 0) { // full transparency
                $color = 'rgba(0,0,0,0)'; // or 'transparent' keyword
            } else {
                if ($type === 'hsla') {
                    $colorTmp[0] = $this->compressAngle($colorTmp[0]);
                } else if ($colorTmp[0] == 255 && $colorTmp[1] == 255 && $colorTmp[2] == 255) {
                    $colorTmp[0] = $colorTmp[1] = 0;
                    $colorTmp[2] = '100%';
                    $type = 'hsla';
                }
                $compressedAlpha = $this->optimiseNumber->compressNumber($colorTmp[3]);
                $color = "$type($colorTmp[0],$colorTmp[1],$colorTmp[2],$compressedAlpha)";
            }
        } else {
            $color = $this->fixBadColorName($color);
        }

        $color = $this->mergeSameHex($color);

        if (isset($shorterNames[strtolower($color)])) {
            $color = $shorterNames[strtolower($color)];
        }

        if ($original != $color) {
            $this->logger->log("Optimised color: Changed '{$original}' to '{$color}'", Logger::INFORMATION);
        }

        return $color;
    }

    /**
     * Merge 6 hex value color with # to 3
     * Example: #ffccbb -> #fcb
     * @param string $color
     * @return string
     */
    protected function mergeSameHex($color)
    {
            // strlen($color) === 7
        if (isset($color{6}) && !isset($color{7}) && $color{0} === '#') {
            $color = strtolower($color); // Lower hex color for better gziping

            // #aabbcc -> #abc
            if ($color{1} === $color{2} && $color{3} === $color{4} && $color{5} === $color{6}) {
                $color = '#' . $color{1} . $color{3} . $color{5};
            }
        }

        return $color;
    }

    /**
     * @see self::$replaceColors
     * @param string $color
     * @return string
     */
    protected function fixBadColorName($color)
    {
        $colorName = strtolower($color);
        if (isset(self::$replaceColors[$colorName])) {
            $temp = self::$replaceColors[$colorName];
            $this->logger->log("Fixed invalid color name: Changed '{$color}' to '{$temp}'", Logger::WARNING);
            return $temp;
        }

        return $color;
    }

    /**
     * Convert from [R, G, B] array to hex string. Array item must be number from 0-255 or
     * percentage value.
     *
     * @param array $parts [R, G, B]
     * @return string
     */
    protected function convertRgbToHex(array $parts)
    {
        foreach ($parts as &$part) {
            $part = trim($part);

            if (substr($part, -1) === '%') {
                $part = round((255 * $part) / 100);
            }
        }

        return $this->threeByteArrayToHex($parts);
    }

    /**
     * Convert color from HSL to HEX RGB
     * @param array $parts [H, S, L]
     * @return string HEX color
     */
    protected function convertHslToHex(array $parts)
    {
        list ($h, $s, $l) = $parts;

        $h = $this->normalizeAngle($h) / 360;

        $normalizeSOrL = function($value, $name) {
            if (!substr($value, -1) === '%') {
                $this->logger->log("HSL $name must be a percent value", Logger::WARNING);
            }

            if ((int) $value > 100) {
                $this->logger->log("HSL $name must be lower than 100%", Logger::WARNING);
            }

            return (int) $value / 100;
        };

        $s = $normalizeSOrL($s, 'saturation');
        $l = $normalizeSOrL($l, 'light');

        $m2 = ($l <= 0.5) ? $l * ($s + 1) : ($l + $s - $l * $s);
        $m1 = $l * 2 - $m2;

        $output = array();

        foreach (array($h + 1/3, $h, $h - 1/3) as $mH) {
            if ($mH < 0) {
                ++$mH;
            } else if ($mH > 1) {
                --$mH;
            }

            if ($mH * 6 < 1) {
                $output[] = $m1 + ($m2 - $m1) * $mH * 6;
            } else if ($mH * 2 < 1) {
                $output[] = $m2;
            } else if ($mH * 3 < 2) {
                $output[] = $m1 + ($m2 - $m1) * (2/3 - $mH) * 6;
            } else {
                $output[] = $m1;
            }
        }

        // Convert back to 1 -> 255
        foreach ($output as &$rgb) {
            $rgb = round($rgb * 255);
        }

        return $this->threeByteArrayToHex($output);
    }

    /**
     * Convert array with three item from 0-255 to corresponding HEX value.
     * @param array $array [R, G, B]
     * @return string Color (in format #ffffff)
     */
    protected function threeByteArrayToHex(array $array)
    {
        $hex = '#';

        foreach ($array as $byte) {
            if ($byte < 16) {
                if ($byte < 0) {
                    $byte = 0;
                }
                $hex .= '0' . dechex($byte);
            } else {
                if ($byte > 255) {
                    $byte = 255;
                }
                $hex .= dechex($byte);
            }
        }

        return $hex;
    }

    /**
     * Normalize angle from 0 to 359
     * @param int $angle
     * @return int
     */
    protected function normalizeAngle($angle)
    {
        return (($angle % 360) + 360) % 360; // normalize from 0 to 359
    }

    /**
     * Convert angle from for example 355 to -5
     * @param int $angle
     * @return int
     */
    protected function compressAngle($angle)
    {
        $this->normalizeAngle($angle);

        if ($angle > 350) {
            $angle -= 360;
        }

        return $angle;
    }
}