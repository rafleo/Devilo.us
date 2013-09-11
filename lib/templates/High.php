<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * CSS High compression output template
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
namespace CSSTidy\Template;

require_once __DIR__ . '/Standard.php';

class High extends Standard
{
    public $atRuleClosingBracket = "\n<span class=\"format\">}</span>\n";

    public $selectorOpeningBracket = "</span><span class=\"format\">{</span>";

    public $afterValueWithSemicolon = "</span><span class=\"format\">;</span>";

    public $spaceBetweenBlocks = "\n";

    public $afterComment = "</span>";
}