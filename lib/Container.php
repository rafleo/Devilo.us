<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Simple Dependency injection container
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

/**
 * @property \CSSTidy\Logger $logger
 * @property \CSSTidy\Configuration $configuration
 * @property \CSSTidy\Parser $parser
 * @property \CSSTidy\SelectorManipulate $selectorManipulate
 * @property \CSSTidy\Sorter $sorter
 * @property \CSSTidy\Optimise\Value $optimiseValue
 * @property \CSSTidy\Optimise\Color $optimiseColor
 * @property \CSSTidy\Optimise\Number $optimiseNumber
 * @property \CSSTidy\Optimise\Shorthand $optimiseShorthand
 * @property \CSSTidy\Optimise\LineAt $optimiseLineAt
 * @property \CSSTidy\Optimise\Selector $optimiseSelector
 */
class Container
{
    /** @var object[] */
    protected $services = array();

    /**
     * Service definition
     */
    public function __construct()
    {
        $cont = $this;
        $this->services = array(
            'logger' => function() {
                require_once __DIR__ . '/Logger.php';
                return new Logger;
            },
            'configuration' => function() {
                require_once __DIR__ . '/Configuration.php';
                return new Configuration;
            },
            'parser' => function() use ($cont) {
                require_once __DIR__ . '/Parser.php';
                return new Parser(
                    $cont->logger,
                    $cont->configuration->getDiscardInvalidProperties(),
                    $cont->configuration->getCssLevel(),
                    $cont->configuration->getRemoveBackSlash()
                );
            },
            'selectorManipulate' => function() {
                require_once __DIR__ . '/SelectorManipulate.php';
                return new SelectorManipulate;
            },
            'sorter' => function() {
                require_once __DIR__ . '/Sorter.php';
                return new Sorter;
            },
            'optimiseValue' => function() use ($cont) {
                require_once __DIR__ . '/optimise/Value.php';
                return new \CSSTidy\Optimise\Value(
                    $cont->logger,
                    $cont->configuration,
                    $cont->optimiseColor,
                    $cont->optimiseNumber
                );
            },
            'optimiseColor' => function() use($cont) {
                require_once __DIR__ . '/optimise/Color.php';
                return new \CSSTidy\Optimise\Color($cont->logger, $cont->optimiseNumber);
            },
            'optimiseNumber' => function() use($cont) {
                require_once __DIR__ . '/optimise/Number.php';
                return new \CSSTidy\Optimise\Number($cont->logger, $cont->configuration->getConvertUnit());
            },
            'optimiseShorthand' => function() use($cont) {
                require_once __DIR__ . '/optimise/Shorthand.php';
                return new \CSSTidy\Optimise\Shorthand($cont->configuration->getOptimiseShorthands());
            },
            'optimiseLineAt' => function() use($cont) {
                require_once __DIR__ . '/optimise/LineAt.php';
                return new \CSSTidy\Optimise\LineAt($cont->logger);
            },
            'optimiseSelector' => function() use($cont) {
                require_once __DIR__ . '/optimise/Selector.php';
                return new \CSSTidy\Optimise\Selector($cont->logger);
            },
        );
    }

    /**
     * @param string $name
     * @return object
     * @throws \Exception
     */
    public function __get($name)
    {
        if (isset($this->services[$name])) {
            return $this->$name = $this->services[$name]();
        }

        throw new \Exception("Service with name '$name' not exists");
    }

    /**
     * @param string $name
     * @param object $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (!is_object($value)) {
            throw new \Exception("Service '$name' must be object");
        }

        $this->$name = $value;
    }
}