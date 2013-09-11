<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Abstract output template
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
 * @author Jakub Onderka (acci at acci dot cz) 2011
 */
namespace CSSTidy;

class Template
{
    /** @var string */
    public $beforeAtRule;

    /** @var string */
    public $atRuleClosingBracket;

    /** @var string */
    public $indentInAtRule;

    /** @var string */
    public $lastLineInAtRule;

    /** @var string */
    public $bracketAfterAtRule;

    /** @var string */
    public $beforeSelector;

    /** @var string */
    public $selectorOpeningBracket;

    /** @var string */
    public $beforeProperty;

    /** @var string */
    public $beforeValue;

    /** @var string */
    public $afterValueWithSemicolon;

    /** @var string */
    public $selectorClosingBracket;

    /** @var string */
    public $spaceBetweenBlocks;

    /** @var string */
    public $beforeComment;

    /** @var string */
    public $afterComment;

    /**
     * Returns template clone without HTML tags for plain output
     * @return Template
     */
    public function getWithoutHtml()
    {
        $return = clone $this;
        foreach ($return as &$value) {
            $value = strip_tags($value);
        }

        return $return;
    }

    /**
     * Convert string template separated with '|' character to instance of Template class
     *
     * @static
     * @param string $content
     * @return Template
     * @throws \Exception
     */
    public static function loadFromString($content)
    {
        $content = strip_tags($content, '<span>');
        $content = str_replace("\r\n", "\n", $content); // Unify newlines (because the output also only uses \n)
        $parts = explode('|', $content);

        if (count($parts) !== 14) {
            throw new \Exception("Template must contains 14 parts");
        }

        $template = new self;
        $template->beforeAtRule = $parts[0];
        $template->bracketAfterAtRule = $parts[1];
        $template->beforeSelector = $parts[2];
        $template->selectorOpeningBracket = $parts[3];
        $template->beforeProperty = $parts[4];
        $template->beforeValue = $parts[5];
        $template->afterValueWithSemicolon = $parts[6];
        $template->selectorClosingBracket = $parts[7];
        $template->spaceBetweenBlocks = $parts[8];
        $template->atRuleClosingBracket = $parts[9];
        $template->indentInAtRule = $parts[10];
        $template->beforeComment = $parts[11];
        $template->afterComment = $parts[12];
        $template->lastLineInAtRule = $parts[13];

        return $template;
    }
}