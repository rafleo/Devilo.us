<?php
namespace CSSTidy;

class Sorter
{
    /**
     * Sort selectors inside at block
     * @param Element\AtBlock $block
     */
    public function sortSelectors(Element\AtBlock $block)
    {
        uasort($block->elements, function($a, $b) {
            if (!$a instanceof Element\Selector || !$b instanceof Element\Selector) {
                return 0;
            }

            return strcasecmp($a->getName(), $b->getName());
        });

        foreach ($block->elements as $element) {
            if ($element instanceof Element\AtBlock) {
                $this->sortSelectors($element);
            }
        }
    }

    /**
     * Sort properties inside block with right order IE hacks
     * @param Element\Block $block
     */
    public function sortProperties(Element\Block $block)
    {
        uksort($block->elements, function($a, $b) {
            static $ieHacks = array(
                '*' => 1, // IE7 hacks first
                '_' => 2, // IE6 hacks
                '/' => 2, // IE6 hacks
                '-' => 2  // IE6 hacks
            );

            if ($a{0} === '!' || $b{0} === '!') { // Compared keys are for selector, not for properties
                return 0;
            } else if (!isset($ieHacks[$a{0}]) && !isset($ieHacks[$b{0}])) {
                return strcasecmp($a, $b);
            } else if (isset($ieHacks[$a{0}]) && !isset($ieHacks[$b{0}])) {
                return 1;
            } else if (!isset($ieHacks[$a{0}]) && isset($ieHacks[$b{0}])) {
                return -1;
            } else if ($ieHacks[$a{0}] === $ieHacks[$b{0}]) {
                return strcasecmp(substr($a, 1), substr($b, 1));
            } else {
                return $ieHacks[$a{0}] > $ieHacks[$b{0}] ? 1 : -1;
            }
        });

        foreach ($block->elements as $element) {
            if ($element instanceof Element\Block) {
                $this->sortProperties($element);
            }
        }
    }
}