<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Class which storage parsed CSS in two forms: tokens and elements
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

require_once __DIR__ . '/Element.php';
require_once __DIR__ . '/Block.php';
require_once __DIR__ . '/Selector.php';
require_once __DIR__ . '/AtBlock.php';
require_once __DIR__ . '/LineAt.php';
require_once __DIR__ . '/Comment.php';
require_once __DIR__ . '/Property.php';

class Root extends AtBlock
{
    /** @var string */
    public $charset = '';

    /** @var LineAt[] */
    public $import = array();

    /** @var LineAt[] */
    public $namespace = array();

    // Overwrite Block constructor
    public function __construct()
    {

    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return !(empty($this->elements) && empty($this->import) && empty($this->charset) && empty($this->namespace));
    }
}