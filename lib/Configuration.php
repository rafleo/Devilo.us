<?php
/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * Configuration
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

class Configuration
{
    /*
      Constants for optimiseShorthands
      1 common shorthands optimization
      2 + font property optimization
      3 + background property optimization
     */
    const
        NOTHING = 0,
        COMMON = 1,
        FONT = 2,
        BACKGROUND = 3;

    // Constans for mergeSelectors
    const DO_NOT_CHANGE = 0,
        SEPARATE_SELECTORS = 1,
        MERGE_SELECTORS = 2;

    // Constants for cssLevel
    const CSS1_0 = 'CSS1.0',
        CSS2_0 = 'CSS2.0',
        CSS2_1 = 'CSS2.1',
        CSS3_0 = 'CSS3.0',
		CSS3_COMP = 'CSS3COMP';

    // Constants for predefinedTemplate
    const STANDARD_COMPRESSION = 'standard',
        HIGHEST_COMPRESSION = 'highest',
        HIGH_COMPRESSION = 'high',
        LOW_COMPRESSION = 'low';

    // Constants for caseProperties
    const NONE = 0,
        LOWERCASE = 1,
        UPPERCASE = 2;

    /** @var bool */
    protected $preserveComments = false;

    /**
     * Rewrite all properties with low case, better for later gzip OK, safe
     * @var int
     */
    protected $caseProperties = self::LOWERCASE;

    /** @var bool */
    protected $lowerCaseSelectors = false;

    /** @var bool */
    protected $removeLastSemicolon = true;

    /** @var bool */
    protected $removeBackSlash = true;

    /**
     * is dangeroues to be used: CSS is broken sometimes
     * @var int
     */
    protected $mergeSelectors = self::DO_NOT_CHANGE;

    /**
     * sort properties in alpabetic order, better for later gzip
     * but can cause trouble in case of overiding same propertie or using hack
     * @var bool
     */
    protected $sortProperties = false;

    /** @var bool */
    protected $sortSelectors = false;

    /** @var bool */
    protected $discardInvalidProperties = false;

    /**
     * Preserve or not browser hacks
     * @var bool
     */
    protected $discardInvalidSelectors = false;

    /** @var int */
    protected $optimiseShorthands = self::COMMON;

    /** @var bool */
    protected $compressFontWeight = true;

    /** @var bool */
    protected $compressColors = true;

    /** @var bool */
    protected $convertUnit = false;

    /** @var bool */
    protected $addTimestamp = false;

    /** @var string */
    protected $cssLevel = self::CSS3_COMP;

    /** @var Template */
    protected $template;

    /** @var string */
    protected $predefinedTemplateName = self::STANDARD_COMPRESSION;

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = array())
    {
        static $oldToNew = array(
            'sort_properties'            => array('sortProperties', 'bool'),
            'sort_selectors'             => array('sortSelectors', 'bool'),
            'discard_invalid_properties' => array('discardInvalidProperties', 'bool'),
            'discard_invalid_selectors'  => array('discardInvalidSelectors', 'bool'),
            'optimise_shorthands'        => array('optimiseShorthands', 'int'),
            'css_level'                  => array('cssLevel', 'string'),
            'merge_selectors'            => array('mergeSelectors', 'int'),
            'compress_font-weight'       => array('compressFontWeight', 'bool'),
            'convert_unit'               => array('convertUnit', 'bool'),
        );
        
        foreach ($configuration as $key => $value) {
            if (isset($oldToNew[$key])) {
                list($newName, $dataType) = $oldToNew[$key];
                settype($value, $dataType);
                $this->{'set' . ucfirst($newName)}($value);
            } else if ($key !== 'template') {
                throw new \Exception("Old configuration '$key' cannot be translated to new");
            }
        }

        if (isset($configuration['template'])) {
            switch ($configuration['template']) {
                case 'highest':
                    $this->loadPredefinedTemplate(self::HIGHEST_COMPRESSION);
                    break;

                case 'high':
                    $this->loadPredefinedTemplate(self::HIGH_COMPRESSION);
                    break;

                case 'low':
                    $this->loadPredefinedTemplate(self::LOW_COMPRESSION);
                    break;

                case 'default':
                case 'standard':
                    $this->loadPredefinedTemplate(self::STANDARD_COMPRESSION);
                    break;

                default:
                    throw new \Exception("Cannot translate template name '{$configuration['template']}' to new version");
            }
        }
    }

    /**
     * @param int $caseProperties
     */
    public function setCaseProperties($caseProperties)
    {
        if (!in_array($caseProperties, array(self::NONE, self::UPPERCASE, self::LOWERCASE))) {
            throw new \InvalidArgumentException(
                "caseProperties must be NONE, UPPERCASE or LOWERCASE constants, $caseProperties given"
            );
        }

        $this->caseProperties = $caseProperties;
    }

    /**
     * @return int
     */
    public function getCaseProperties()
    {
        return $this->caseProperties;
    }

    /**
     * @param bool $compressColors
     */
    public function setCompressColors($compressColors = true)
    {
        $this->checkBool(__FUNCTION__, $compressColors);
        $this->compressColors = $compressColors;
    }

    /**
     * @return bool
     */
    public function getCompressColors()
    {
        return $this->compressColors;
    }

    /**
     * @param boolean $convertUnit
     */
    public function setConvertUnit($convertUnit = true)
    {
        $this->checkBool(__FUNCTION__, $convertUnit);
        $this->convertUnit = $convertUnit;
    }

    /**
     * @return boolean
     */
    public function getConvertUnit()
    {
        return $this->convertUnit;
    }

    /**
     * @param bool $compressFontWeight
     */
    public function setCompressFontWeight($compressFontWeight = true)
    {
        $this->checkBool(__FUNCTION__, $compressFontWeight);
        $this->compressFontWeight = $compressFontWeight;
    }

    /**
     * @return bool
     */
    public function getCompressFontWeight()
    {
        return $this->compressFontWeight;
    }

    /**
     * @param string $cssLevel
     */
    public function setCssLevel($cssLevel)
    {
        if (!in_array($cssLevel, array(self::CSS1_0, self::CSS2_0, self::CSS2_1, self::CSS3_0), true)) {
            throw new \InvalidArgumentException(
                "cssLevel must be CSS1_0, CSS2_0, CSS2_1 or CSS3_0 constants, $cssLevel given"
            );
        }

        $this->cssLevel = $cssLevel;
    }

    /**
     * @return string
     */
    public function getCssLevel()
    {
        return $this->cssLevel;
    }

    /**
     * @param bool $discardInvalidProperties
     */
    public function setDiscardInvalidProperties($discardInvalidProperties = true)
    {
        $this->checkBool(__FUNCTION__, $discardInvalidProperties);
        $this->discardInvalidProperties = $discardInvalidProperties;
    }

    /**
     * @return bool
     */
    public function getDiscardInvalidProperties()
    {
        return $this->discardInvalidProperties;
    }

    /**
     * @param bool $discardInvalidSelectors
     */
    public function setDiscardInvalidSelectors($discardInvalidSelectors = true)
    {
        $this->checkBool(__FUNCTION__, $discardInvalidSelectors);
        $this->discardInvalidSelectors = $discardInvalidSelectors;
    }

    /**
     * @return bool
     */
    public function getDiscardInvalidSelectors()
    {
        return $this->discardInvalidSelectors;
    }

    /**
     * @param bool $lowerCaseSelectors
     */
    public function setLowerCaseSelectors($lowerCaseSelectors = true)
    {
        $this->checkBool(__FUNCTION__, $lowerCaseSelectors);
        $this->lowerCaseSelectors = $lowerCaseSelectors;
    }

    /**
     * @return bool
     */
    public function getLowerCaseSelectors()
    {
        return $this->lowerCaseSelectors;
    }

    /**
     * @param int $mergeSelectors
     */
    public function setMergeSelectors($mergeSelectors)
    {
        if (!in_array($mergeSelectors, array(self::DO_NOT_CHANGE, self::SEPARATE_SELECTORS, self::MERGE_SELECTORS), true)) {
            throw new \InvalidArgumentException(
                "mergeSelectors must be DO_NOT_CHANGE, SEPARATE_SELECTORS or MERGE_SELECTORS constants, $mergeSelectors given"
            );
        }

        $this->mergeSelectors = $mergeSelectors;
    }

    /**
     * @return int
     */
    public function getMergeSelectors()
    {
        return $this->mergeSelectors;
    }

    /**
     * @param int $optimiseShorthands
     */
    public function setOptimiseShorthands($optimiseShorthands)
    {
        if (!in_array($optimiseShorthands, array(self::NOTHING, self::COMMON, self::FONT, self::BACKGROUND), true)) {
            throw new \InvalidArgumentException(
                "optimizeShorthands must be COMMON, FONT or BACKGROUND constants, $optimiseShorthands given"
            );
        }

        $this->optimiseShorthands = $optimiseShorthands;
    }

    /**
     * @return int
     */
    public function getOptimiseShorthands()
    {
        return $this->optimiseShorthands;
    }

    /**
     * @param bool $preserveComments
     */
    public function setPreserveComments($preserveComments = true)
    {
        $this->checkBool(__FUNCTION__, $preserveComments);
        $this->preserveComments = $preserveComments;
    }

    /**
     * @return bool
     */
    public function getPreserveComments()
    {
        return $this->preserveComments;
    }

    /**
     * @param bool $removeBackSlash
     */
    public function setRemoveBackSlash($removeBackSlash = true)
    {
        $this->checkBool(__FUNCTION__, $removeBackSlash);
        $this->removeBackSlash = $removeBackSlash;
    }

    /**
     * @return bool
     */
    public function getRemoveBackSlash()
    {
        return $this->removeBackSlash;
    }

    /**
     * @param bool $removeLastSemicolon
     */
    public function setRemoveLastSemicolon($removeLastSemicolon = true)
    {
        $this->checkBool(__FUNCTION__, $removeLastSemicolon);
        $this->removeLastSemicolon = $removeLastSemicolon;
    }

    /**
     * @return bool
     */
    public function getRemoveLastSemicolon()
    {
        return $this->removeLastSemicolon;
    }

    /**
     * @param bool $sortProperties
     */
    public function setSortProperties($sortProperties = true)
    {
        $this->checkBool(__FUNCTION__, $sortProperties);
        $this->sortProperties = $sortProperties;
    }

    /**
     * @return bool
     */
    public function getSortProperties()
    {
        return $this->sortProperties;
    }

    /**
     * @param bool $sortSelectors
     */
    public function setSortSelectors($sortSelectors)
    {
        $this->checkBool(__FUNCTION__, $sortSelectors);
        $this->sortSelectors = $sortSelectors;
    }

    /**
     * @return bool
     */
    public function getSortSelectors()
    {
        return $this->sortSelectors;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
        $this->predefinedTemplateName = null;
    }

    /**
     * @param string $template
     * @throws \Exception
     */
    public function loadPredefinedTemplate($template)
    {
        $location = __DIR__ . "/templates/" . ucfirst($template) . ".php";
        if (!file_exists($location)) {
            throw new \Exception("File with predefined template '$template' not found in '$location'");
        }

        require_once $location;

        $className = "\\CSSTidy\\Template\\" . ucfirst($template);
        if (!class_exists($className)) {
            throw new \Exception("Predefined template with name '$template' not found in file '$location'");
        }

        $this->setTemplate(new $className);
        $this->predefinedTemplateName = $template;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getPredefinedTemplateName()
    {
        if ($this->predefinedTemplateName === null) {
            throw new \Exception('No predefined template is set');
        }

        return $this->predefinedTemplateName;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        if (empty($this->template)) {
            $this->loadPredefinedTemplate($this->predefinedTemplateName);
        }

        return $this->template;
    }

    /**
     * @param bool $timestamp
     */
    public function setAddTimestamp($timestamp = true)
    {
        $this->checkBool(__FUNCTION__, $timestamp);
        $this->addTimestamp = $timestamp;
    }

    /**
     * @return bool
     */
    public function getAddTimestamp()
    {
        return $this->addTimestamp;
    }

    /**
    * @param string $method
    * @param mixed $bool
    * @throws \InvalidArgumentException
    */
    protected function checkBool($method, $bool)
    {
        if (!is_bool($bool)) {
            $type = gettype($bool);
            throw new \InvalidArgumentException("Method $method accept only bool data type, $type given");
        }
    }
}