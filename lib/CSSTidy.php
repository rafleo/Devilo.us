<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * CSS Parser class
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
namespace CSSTidy;

require_once __DIR__ . '/Container.php';
require_once __DIR__ . '/Template.php';
require_once __DIR__ . '/Configuration.php';
require_once __DIR__ . '/Output.php';

/**
 * CSS Parser class
 *
 * This class represents a CSS parser which reads CSS code and saves it in an array.
 * In opposite to most other CSS parsers, it does not use regular expressions and
 * thus has full CSS3 support and a higher reliability.
 * Additional to that it applies some optimisations and fixes to the CSS code.
 * An online version should be available here: http://cdburnerxp.se/cssparse/css_optimiser.php
 * @package CSSTidy
 * @author Florian Schmitz (floele at gmail dot com) 2005-2006
 * @version 1.4
 */
class CSSTidy
{
    /** @var string */
    private static $version = '1.4';

    /** @var \CSSTidy\Container */
    private $container;

    /** @var \CSSTidy\Logger */
    public $logger;

    /** @var \CSSTidy\Configuration */
    public $configuration;

    /**
     * @param Configuration|null $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        $this->container = new Container;

        if ($configuration) {
            $this->configuration = $this->container->configuration = $configuration;
        } else {
            $this->configuration = $this->container->configuration;
        }

        $this->logger = $this->container->logger;
    }

    /**
     * @param $string
     * @return Output
     * @throws \Exception
     */
    public function process($string)
    {
        $original = $string;

        // Temporarily set locale to en_US in order to handle floats properly
        $old = @setlocale(LC_ALL, 0);
        @setlocale(LC_ALL, 'C');

        $parsed = $this->container->parser->parse($string);

        if (!$parsed->isOk()) {
            @setlocale(LC_ALL, $old);
            throw new \Exception("Invalid CSS");
        }

        $this->container->optimiseSelector->process($parsed);
        $this->container->optimiseValue->process($parsed);

        switch ($this->configuration->getMergeSelectors()) {
            case Configuration::SEPARATE_SELECTORS:
                $this->container->selectorManipulate->separate($parsed);
                break;

            case Configuration::MERGE_SELECTORS:
                $this->container->selectorManipulate->mergeWithSameName($parsed);
                $this->container->selectorManipulate->mergeWithSameProperties($parsed);
                break;
        }

        if ($this->configuration->getDiscardInvalidSelectors()) {
            $this->container->selectorManipulate->discardInvalid($parsed);
        }

        if ($this->configuration->getOptimiseShorthands()){
            $this->container->optimiseShorthand->process($parsed);
        }

        foreach ($parsed->import as $import) {
            $this->container->optimiseLineAt->process($import);
        }

        foreach ($parsed->namespace as $namespace) {
            $this->container->optimiseLineAt->process($namespace);
        }

        if ($this->configuration->getSortProperties()) {
            $this->container->sorter->sortProperties($parsed);
        }

        if ($this->configuration->getSortSelectors()) {
            $this->container->sorter->sortSelectors($parsed);
        }

        /*echo '<pre>';
        var_export($parsed->properties);
        echo '</pre>';*/

        @setlocale(LC_ALL, $old); // Set locale back to original setting

        return new Output($this->configuration, $this->logger, $original, $parsed);
    }

    /**
     * @param string $string
     * @param string $fileDirectory
     * @return string
     */
    public function mergeImports($string, $fileDirectory = '')
    {
        preg_match_all('~@import[ \t\r\n\f]*(url\()?("[^\n\r\f\\"]+"|\'[^\n\r\f\\"]+\')(\))?([^;]*);~si', $string, $matches);

        $notResolvedImports = array();
        foreach ($matches[2] as $i => $fileName) {
            $importRule = $matches[0][$i];

            if (trim($matches[4][$i]) !== '') {
                $notResolvedImports[] = $importRule;
                continue; // Import is for other media
            }

            $fileName = trim($fileName, " \t\r\n\f'\"");

            $content = file_get_contents($fileDirectory . $fileName);

            $string = str_replace($importRule, $content ? $content : '', $string);

            if (!$content) {
                $notResolvedImports[] = $importRule;
                $this->logger->log("Import file {$fileDirectory}{$fileName} not found", Logger::WARNING);
            }
        }

        return implode("\n", $notResolvedImports) . $string;
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }
}