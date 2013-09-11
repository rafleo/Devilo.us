<?php
namespace CSSTidy;

require_once __DIR__ . '/elements/Root.php';

class Parser
{
    /**
     * All whitespace allowed in CSS without '\r', because is changed to '\n' before parsing
     * @static
     * @var string
     */
    public static $whitespace = " \n\t\x0B\x0C";

    /**
     * Array is generated from self::$whitespace in __constructor
     * @static
     * @var array
     */
    public static $whitespaceArray = array();

    /**
     * All CSS tokens used by csstidy
     *
     * @var string
     * @static
     */
    public static $tokensList = '/@}{;:=\'"(,\\!$%&)*+.<>?[]^`|~';

    /**
     * All properties, value contains comma separated CSS supported versions
     *
     * @static
     * @var array
     */
    public static $allProperties = array(
        'background' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'background-color' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'background-image' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'background-repeat' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'background-attachment' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'background-position' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-top' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-right' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-bottom' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-left' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-color' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-top-color' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-bottom-color' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-left-color' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-right-color' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-style' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-top-style' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-right-style' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-left-style' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-bottom-style' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-width' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-top-width' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-right-width' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-left-width' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-bottom-width' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-collapse' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'border-spacing' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'bottom' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'caption-side' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'content' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'clear' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'clip' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'color' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'counter-reset' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'counter-increment' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'cursor' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'empty-cells' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'display' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'direction' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'float' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'font' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'font-family' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'font-style' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'font-variant' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'font-weight' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'font-stretch' => 'CSS2.0,CSS3.0,CSS3COMP',
        'font-size-adjust' => 'CSS2.0,CSS3.0,CSS3COMP',
        'font-size' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'height' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'left' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'line-height' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'list-style' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'list-style-type' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'list-style-image' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'list-style-position' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'margin' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'margin-top' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'margin-right' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'margin-bottom' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'margin-left' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'marks' => 'CSS1.0,CSS2.0,CSS3.0,CSS3COMP',
        'marker-offset' => 'CSS2.0,CSS3.0,CSS3COMP',
        'max-height' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'max-width' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'min-height' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'min-width' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'overflow' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'orphans' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'outline' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'outline-width' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'outline-style' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'outline-color' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'padding' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'padding-top' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'padding-right' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'padding-bottom' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'padding-left' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'page-break-before' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'page-break-after' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'page-break-inside' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'page' => 'CSS2.0,CSS3.0,CSS3COMP',
        'position' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'quotes' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'right' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'size' => 'CSS1.0,CSS2.0,CSS3.0,CSS3COMP',
        'speak-header' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'table-layout' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'top' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'text-indent' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'text-align' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'text-decoration' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'text-shadow' => 'CSS2.0,CSS3.0,CSS3COMP',
        'letter-spacing' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'word-spacing' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'text-transform' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'white-space' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'unicode-bidi' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'vertical-align' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'visibility' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'width' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'widows' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'z-index' => 'CSS1.0,CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'volume' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'speak' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'pause' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'pause-before' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'pause-after' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'cue' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'cue-before' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'cue-after' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'play-during' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'azimuth' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'elevation' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'speech-rate' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'voice-family' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'pitch' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'pitch-range' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'stress' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'richness' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'speak-punctuation' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',
        'speak-numeral' => 'CSS2.0,CSS2.1,CSS3.0,CSS3COMP',

        // CSS3 properties
        // Animation module
        'animation-timing-function' => 'CSS3.0,CSS3COMP',
        'animation-name' => 'CSS3.0,CSS3COMP',
        'animation-duration' => 'CSS3.0,CSS3COMP',
        'animation-iteration-count' => 'CSS3.0,CSS3COMP',
        'animation-direction' => 'CSS3.0,CSS3COMP',
        'animation-play-state' => 'CSS3.0,CSS3COMP',
        'animation-delay' => 'CSS3.0,CSS3COMP',
        'animation' => 'CSS3.0,CSS3COMP',
        // Backgrounds
        'background-size' => 'CSS3.0,CSS3COMP',
        'background-origin' => 'CSS3.0,CSS3COMP',
        'border-radius' => 'CSS3.0,CSS3COMP',
        'border-top-right-radius' => 'CSS3.0,CSS3COMP',
        'border-bottom-right-radius' => 'CSS3.0,CSS3COMP',
        'border-bottom-left-radius' => 'CSS3.0,CSS3COMP',
        'border-top-left-radius' => 'CSS3.0,CSS3COMP',
        'border-image' => 'CSS3.0,CSS3COMP',
        'border-top-left-radius' => 'CSS3.0,CSS3COMP',
        'border-top-right-radius' => 'CSS3.0,CSS3COMP',
        'border-bottom-right-radius' => 'CSS3.0,CSS3COMP',
        'border-bottom-left-radius' => 'CSS3.0,CSS3COMP',
        'box-shadow' => 'CSS3.0,CSS3COMP',
        // Font module
        'src' => 'CSS3.0,CSS3COMP', // inside @font-face
        'font-variant-east-asian' => 'CSS3.0,CSS3COMP',
        'font-variant-numeric' => 'CSS3.0,CSS3COMP',
        'font-variant-ligatures' => 'CSS3.0,CSS3COMP',
        'font-feature-settings' => 'CSS3.0,CSS3COMP',
        'font-language-override' => 'CSS3.0,CSS3COMP',
        'font-kerning' => 'CSS3.0,CSS3COMP',
        // Color Module
        'opacity' => 'CSS3.0,CSS3COMP',
        // Box module
        'overflow-x' => 'CSS3.0,CSS3COMP',
        'overflow-y' => 'CSS3.0,CSS3COMP',
        // UI module
        'pointer-events' => 'CSS3.0,CSS3COMP',
        'user-select' => 'CSS3.0,CSS3COMP',
        // Images
        'image-rendering' => 'CSS3.0,CSS3COMP',
        'image-resolution' => 'CSS3.0,CSS3COMP',
        'image-orientation' => 'CSS3.0,CSS3COMP',
        // Transform
        'transform' => 'CSS3.0,CSS3COMP',
        'transform-origin' => 'CSS3.0,CSS3COMP',
        'transform-style' => 'CSS3.0,CSS3COMP',
        'perspective' => 'CSS3.0,CSS3COMP',
        'perspective-origin' => 'CSS3.0,CSS3COMP',
        'backface-visibility' => 'CSS3.0,CSS3COMP',
        // Transition
        'transition' => 'CSS3.0,CSS3COMP',
        'transition-delay' => 'CSS3.0,CSS3COMP',
        'transition-duration' => 'CSS3.0,CSS3COMP',
        'transition-property' => 'CSS3.0,CSS3COMP',
        'transition-timing-function' => 'CSS3.0,CSS3COMP',
        // Speech
        'voice-pitch' => 'CSS3.0,CSS3COMP',
		
		
		// CSS3 COMPATIBLE
		/* CSS3 Compatibility-Mode (includes hacks for greater browser support) */
		/* gecko */
        '-moz-appearance' => 'CSS3COMP',
        '-moz-background-clip' => 'CSS3COMP',
        '-moz-background-inline-policy' => 'CSS3COMP',
        '-moz-background-origin' => 'CSS3COMP',
        '-moz-background-size' => 'CSS3COMP',
        '-moz-binding' => 'CSS3COMP',
        '-moz-border-bottom-colors' => 'CSS3COMP',
        '-moz-border-left-colors' => 'CSS3COMP',
        '-moz-border-right-colors' => 'CSS3COMP',
        '-moz-border-top-colors' => 'CSS3COMP',
        '-moz-border-end' => 'CSS3COMP',
        '-moz-border-end-color' => 'CSS3COMP',
        '-moz-border-end-style' => 'CSS3COMP',
        '-moz-border-end-width' => 'CSS3COMP',
        '-moz-border-image' => 'CSS3COMP',
        '-moz-border-radius' => 'CSS3COMP',
        '-moz-border-radius-bottomleft' => 'CSS3COMP',
        '-moz-border-radius-bottomright' => 'CSS3COMP',
        '-moz-border-radius-topleft' => 'CSS3COMP',
        '-moz-border-radius-topright' => 'CSS3COMP',
        '-moz-border-start' => 'CSS3COMP',
        '-moz-border-start-color' => 'CSS3COMP',
        '-moz-border-start-style' => 'CSS3COMP',
        '-moz-border-start-width' => 'CSS3COMP',
        '-moz-box-align' => 'CSS3COMP',
        '-moz-box-direction' => 'CSS3COMP',
        '-moz-box-flex' => 'CSS3COMP',
        '-moz-box-flexgroup' => 'CSS3COMP',
        '-moz-box-ordinal-group' => 'CSS3COMP',
        '-moz-box-orient' => 'CSS3COMP',
        '-moz-box-pack' => 'CSS3COMP',
        '-moz-box-shadow' => 'CSS3COMP',
        '-moz-box-sizing' => 'CSS3COMP',
        '-moz-column-count' => 'CSS3COMP',
        '-moz-column-gap' => 'CSS3COMP',
        '-moz-column-width' => 'CSS3COMP',
        '-moz-column-rule' => 'CSS3COMP',
        '-moz-column-rule-width' => 'CSS3COMP',
        '-moz-column-rule-style' => 'CSS3COMP',
        '-moz-column-rule-color' => 'CSS3COMP',
        '-moz-float-edge' => 'CSS3COMP',
        '-moz-force-broken-image-icon' => 'CSS3COMP',
        '-moz-image-region' => 'CSS3COMP',
        '-moz-margin-end' => 'CSS3COMP',
        '-moz-margin-start' => 'CSS3COMP',
        '-moz-outline' => 'CSS3COMP',
        '-moz-outline-radius' => 'CSS3COMP',
        '-moz-outline-radius-bottomleft' => 'CSS3COMP',
        '-moz-outline-radius-bottomright' => 'CSS3COMP',
        '-moz-outline-radius-topleft' => 'CSS3COMP',
        '-moz-outline-radius-topright' => 'CSS3COMP',
        '-moz-padding-end' => 'CSS3COMP',
        '-moz-padding-start' => 'CSS3COMP',
        '-moz-stack-sizing' => 'CSS3COMP',
        '-moz-transform' => 'CSS3COMP',
        '-moz-transform-origin' => 'CSS3COMP',
        '-moz-transition' => 'CSS3COMP',
        '-moz-transition-delay' => 'CSS3COMP',
        '-moz-transition-duration' => 'CSS3COMP',
        '-moz-transition-property' => 'CSS3COMP',
        '-moz-transition-timing-function' => 'CSS3COMP',
        '-moz-user-focus' => 'CSS3COMP',
        '-moz-user-input' => 'CSS3COMP',
        '-moz-user-modify' => 'CSS3COMP',
        '-moz-user-select' => 'CSS3COMP',
        '-moz-window-shadow' => 'CSS3COMP',
		/* webkit */
        '-webkit-animation' => 'CSS3COMP',
        '-webkit-animation-delay' => 'CSS3COMP',
        '-webkit-animation-direction' => 'CSS3COMP',
        '-webkit-animation-duration' => 'CSS3COMP',
        '-webkit-animation-iteration-count' => 'CSS3COMP',
        '-webkit-animation-name' => 'CSS3COMP',
        '-webkit-animation-play-state' => 'CSS3COMP',
        '-webkit-animation-timing-function' => 'CSS3COMP',
        '-webkit-appearance' => 'CSS3COMP',
        '-webkit-backface-visibility' => 'CSS3COMP',
        '-webkit-background-clip' => 'CSS3COMP',
        '-webkit-background-composite' => 'CSS3COMP',
        '-webkit-background-origin' => 'CSS3COMP',
        '-webkit-background-size' => 'CSS3COMP',
        '-webkit-border-bottom-left-radius' => 'CSS3COMP',
        '-webkit-border-bottom-right-radius' => 'CSS3COMP',
        '-webkit-border-horizontal-spacing' => 'CSS3COMP',
        '-webkit-border-image' => 'CSS3COMP',
        '-webkit-border-radius' => 'CSS3COMP',
        '-webkit-border-top-left-radius' => 'CSS3COMP',
        '-webkit-border-top-right-radius' => 'CSS3COMP',
        '-webkit-border-vertical-spacing' => 'CSS3COMP',
        '-webkit-box-align' => 'CSS3COMP',
        '-webkit-box-direction' => 'CSS3COMP',
        '-webkit-box-flex' => 'CSS3COMP',
        '-webkit-box-flex-group' => 'CSS3COMP',
        '-webkit-box-lines' => 'CSS3COMP',
        '-webkit-box-ordinal-group' => 'CSS3COMP',
        '-webkit-box-orient' => 'CSS3COMP',
        '-webkit-box-pack' => 'CSS3COMP',
        '-webkit-box-reflect' => 'CSS3COMP',
        '-webkit-box-shadow' => 'CSS3COMP',
        '-webkit-box-sizing' => 'CSS3COMP',
        '-webkit-column-break-after' => 'CSS3COMP',
        '-webkit-column-break-before' => 'CSS3COMP',
        '-webkit-column-break-inside' => 'CSS3COMP',
        '-webkit-column-count' => 'CSS3COMP',
        '-webkit-column-gap' => 'CSS3COMP',
        '-webkit-column-rule' => 'CSS3COMP',
        '-webkit-column-rule-color' => 'CSS3COMP',
        '-webkit-column-rule-style' => 'CSS3COMP',
        '-webkit-column-rule-width' => 'CSS3COMP',
        '-webkit-column-width' => 'CSS3COMP',
        '-webkit-columns' => 'CSS3COMP',
        '-webkit-dashboard-region' => 'CSS3COMP',
        '-webkit-line-break' => 'CSS3COMP',
        '-webkit-margin-bottom-collapse' => 'CSS3COMP',
        '-webkit-margin-collapse' => 'CSS3COMP',
        '-webkit-margin-start' => 'CSS3COMP',
        '-webkit-margin-top-collapse' => 'CSS3COMP',
        '-webkit-marquee' => 'CSS3COMP',
        '-webkit-marquee-direction' => 'CSS3COMP',
        '-webkit-marquee-increment' => 'CSS3COMP',
        '-webkit-marquee-repetition' => 'CSS3COMP',
        '-webkit-marquee-speed' => 'CSS3COMP',
        '-webkit-marquee-style' => 'CSS3COMP',
        '-webkit-mask' => 'CSS3COMP',
        '-webkit-mask-attachment' => 'CSS3COMP',
        '-webkit-mask-box-image' => 'CSS3COMP',
        '-webkit-mask-clip' => 'CSS3COMP',
        '-webkit-mask-composite' => 'CSS3COMP',
        '-webkit-mask-image' => 'CSS3COMP',
        '-webkit-mask-origin' => 'CSS3COMP',
        '-webkit-mask-position' => 'CSS3COMP',
        '-webkit-mask-position-x' => 'CSS3COMP',
        '-webkit-mask-position-y' => 'CSS3COMP',
        '-webkit-mask-repeat' => 'CSS3COMP',
        '-webkit-mask-size' => 'CSS3COMP',
        '-webkit-nbsp-mode' => 'CSS3COMP',
        '-webkit-padding-start' => 'CSS3COMP',
        '-webkit-perspective' => 'CSS3COMP',
        '-webkit-perspective-origin' => 'CSS3COMP',
        '-webkit-rtl-ordering' => 'CSS3COMP',
        '-webkit-tap-highlight-color' => 'CSS3COMP',
        '-webkit-text-fill-color' => 'CSS3COMP',
        '-webkit-text-security' => 'CSS3COMP',
        '-webkit-text-size-adjust' => 'CSS3COMP',
        '-webkit-text-stroke' => 'CSS3COMP',
        '-webkit-text-stroke-color' => 'CSS3COMP',
        '-webkit-text-stroke-width' => 'CSS3COMP',
        '-webkit-touch-callout' => 'CSS3COMP',
        '-webkit-transform' => 'CSS3COMP',
        '-webkit-transform-origin' => 'CSS3COMP',
        '-webkit-transform-origin-x' => 'CSS3COMP',
        '-webkit-transform-origin-y' => 'CSS3COMP',
        '-webkit-transform-origin-z' => 'CSS3COMP',
        '-webkit-transform-style' => 'CSS3COMP',
        '-webkit-transition' => 'CSS3COMP',
        '-webkit-transition-delay' => 'CSS3COMP',
        '-webkit-transition-duration' => 'CSS3COMP',
        '-webkit-transition-property' => 'CSS3COMP',
        '-webkit-transition-timing-function' => 'CSS3COMP',
        '-webkit-user-drag' => 'CSS3COMP',
        '-webkit-user-modify' => 'CSS3COMP',
        '-webkit-user-select' => 'CSS3COMP',
		/* opera / presto rendering engine http://choice.opera.com/docs/specs/presto25/css/transitions/ etc. */
        '-o-transition' => 'CSS3COMP',
        '-o-transition-delay' => 'CSS3COMP',
        '-o-transition-timing-function' => 'CSS3COMP',
        '-o-transition-duration' => 'CSS3COMP',
        '-o-transition-property' => 'CSS3COMP',
        '-o-transform' => 'CSS3COMP',
        '-o-transform-origin' => 'CSS3COMP',
        '-o-background-size' => 'CSS3COMP',
        '-o-table-baseline' => 'CSS3COMP',
        '-o-text-overflow' => 'CSS3COMP',
        '-xv-voice-balance' => 'CSS3COMP',
        '-xv-voice-duration' => 'CSS3COMP',
        '-xv-voice-pitch' => 'CSS3COMP',
        '-xv-voice-pitch-range' => 'CSS3COMP',
        '-xv-voice-rate' => 'CSS3COMP',
        '-xv-voice-stress' => 'CSS3COMP',
        '-xv-voice-volume' => 'CSS3COMP',
        '-o-tab-size' => 'CSS3COMP',
		/* internet explorer */
        '-ms-accelerator' => 'CSS3COMP',
        '-ms-background-position-x' => 'CSS3COMP',
        '-ms-background-position-y' => 'CSS3COMP',
        '-ms-behavior' => 'CSS3COMP',
        '-ms-block-progression' => 'CSS3COMP',
        '-ms-filter' => 'CSS3COMP',
        '-ms-ime-mode' => 'CSS3COMP',
        '-ms-layout-grid' => 'CSS3COMP',
        '-ms-layout-grid-char' => 'CSS3COMP',
        '-ms-layout-grid-line' => 'CSS3COMP',
        '-ms-layout-grid-mode' => 'CSS3COMP',
        '-ms-layout-grid-type' => 'CSS3COMP',
        '-ms-line-break' => 'CSS3COMP',
        '-ms-line-grid-mode' => 'CSS3COMP',
        '-ms-interpolation-mode' => 'CSS3COMP', 
        '-ms-overflow-x' => 'CSS3COMP',
        '-ms-overflow-y' => 'CSS3COMP',
        '-ms-scrollbar-3dlight-color' => 'CSS3COMP',
        '-ms-scrollbar-arrow-color' => 'CSS3COMP',
        '-ms-scrollbar-base-color' => 'CSS3COMP',
        '-ms-scrollbar-darkshadow-color' => 'CSS3COMP',
        '-ms-scrollbar-face-color' => 'CSS3COMP',
        '-ms-scrollbar-highlight-color' => 'CSS3COMP',
        '-ms-scrollbar-shadow-color' => 'CSS3COMP',
        '-ms-scrollbar-track-color' => 'CSS3COMP',
        '-ms-text-align-last' => 'CSS3COMP',
        '-ms-text-autospace' => 'CSS3COMP',
        '-ms-text-justify' => 'CSS3COMP',
        '-ms-text-kashida-space' => 'CSS3COMP',
        '-ms-text-overflow' => 'CSS3COMP',
        '-ms-text-underline-position' => 'CSS3COMP',
        '-ms-word-break' => 'CSS3COMP',
        '-ms-word-wrap' => 'CSS3COMP',
        '-ms-writing-mode' => 'CSS3COMP',
        '-ms-zoom' => 'CSS3COMP',
		/* konqueror */
        '-khtml-opacity' => 'CSS3COMP',
        '-khtml-border-radius' => 'CSS3COMP',
		
    );

    /** @var int */
    protected $line;

    /** @var Logger */
    protected $logger;

    /** @var bool */
    protected $discardInvalidProperties;

    /** @var string */
    protected $cssLevel;

    /** @var bool */
    protected $removeBackSlash;

    public function __construct(Logger $logger, $discardInvalidProperties, $cssLevel, $removeBackSlash)
    {
        $this->logger = $logger;
        $this->discardInvalidProperties = $discardInvalidProperties;
        $this->cssLevel = $cssLevel;
        $this->removeBackSlash = $removeBackSlash;

        // Prepare array of all CSS whitespaces
        self::$whitespaceArray = str_split(self::$whitespace);
    }

    /**
     * @param $string
     * @return \CSSTidy\Element\Root
     */
    public function parse($string)
    {
        // Normalize new line characters
        $string = str_replace(array("\r\n", "\r"), array("\n", "\n"), $string) . ' ';

        // Initialize variables
        $currentString = $stringEndsWith = $subValue = $value = $property = $selector = '';
        $bracketCount = 0;
        $this->line = 1;

        /*
         * Possible values:
         * - is = in selector
         * - ip = in property
         * - iv = in value
         * - instr = in string (started at " or ')
         * - inbrck = in bracket (started by ()
         * - at = in @-block
         */
        $status = 'is';
        $subValues = $from = $selectorSeparate = array();

        $root = new Element\Root;
        $stack = array($root);

        for ($i = 0, $size = strlen($string); $i < $size; $i++) {
            $current = $string{$i};

            if ($current === "\n") {
                ++$this->line;
            }

            switch ($status) {
                /* Case in-selector */
                case 'is':
                    if ($this->isToken($string, $i)) {
                        if ($current === '{') {
                            $status = 'ip';
                            $from[] = 'is';
                            $selector = trim($selector);
                            $stack[] = end($stack)->addBlock(new Element\Selector($selector));
                            $this->setSubSelectors(end($stack), $selectorSeparate);
                            $selectorSeparate = array();
                        } else if ($current === ',') {
                            $selector = trim($selector) . ',';
                            $selectorSeparate[] = strlen($selector);
                        } else if ($current === '/' && $string{$i + 1} === '*') {
                            end($stack)->addComment(new Element\Comment($this->parseComment($string, $i)));
                        } else if ($current === '@' && trim($selector) == '') {
                            $status = 'at';
                        } else if ($current === '"' || $current === "'") {
                            $currentString = $stringEndsWith = $current;
                            $status = 'instr';
                            $from[] = 'is';
                        } else if ($current === '}') {
                            array_pop($stack);
                            $selector = '';
                        } else if ($current === '\\') {
                            $selector .= $this->unicode($string, $i);
                        } else {
                            $selector .= $current;
                        }
                    } else {
                        $last = strcspn($string, self::$tokensList . self::$whitespace, $i);
                        if ($last !== 0) {
                            $selector .= substr($string, $i, $last);
                            $i += $last - 1;
                        } else if (
                            !isset($selector{0}) ||
                            !(($last = substr($selector, -1)) === ',' || ctype_space($last))
                        ) {
                            $selector .= $current;
                        }
                    }
                    break;

                /* Case in-property */
                case 'ip':
                    if ($this->isToken($string, $i)) {
                        if (($current === ':' || $current === '=') && isset($property{0})) {
                            $status = 'iv';
                            $from[] = 'ip';
                        } else if ($current === '}') {
                            array_pop($stack);
                            $status = array_pop($from);
                            $selector = $property = '';
                        } else if ($current === '@') {
                            $status = 'at';
                        } else if ($current === '/' && $string{$i + 1} === '*') {
                            end($stack)->addComment(new Element\Comment($this->parseComment($string, $i)));
                        } else if ($current === ';') {
                            $property = '';
                        } else if ($current === '\\') {
                            $property .= $this->unicode($string, $i);
                        } else if ($property == '' && !ctype_space($current)) {
                            $property .= $current;
                        }
                    } else {
                        $last = strcspn($string, self::$tokensList . self::$whitespace, $i);
                        if ($last !== 0) {
                            $property .= substr($string, $i, $last);
                            $i += $last - 1;
                        } else if (!ctype_space($current)) {
                            $property .= $current;
                        }
                    }
                    break;

                /* Case in-value */
                case 'iv':
                    $pn = ($current === "\n" && $this->propertyIsNext($string, $i + 1) || $i === $size - 1);
                    if ($this->isToken($string, $i) || $pn) {
                        if ($current === '/' && $string{$i + 1} === '*') {
                            end($stack)->addComment(new Element\Comment($this->parseComment($string, $i)));
                        } else if ($current === '"' || $current === "'") {
                            $currentString = $stringEndsWith = $current;
                            $status = 'instr';
                            $from[] = 'iv';
                        } else if ($current === '(') {
                            $subValue .= $current;
                            $bracketCount = 1;
                            $status = 'inbrck';
                            $from[] = 'iv';
                        } else if ($current === ',' || $current === '!') {
                            if (($trimmed = trim($subValue, self::$whitespace)) !== '') {
                                $subValues[] = $trimmed;
                                $subValue = '';
                            }
                            $subValues[] = $current;
                        } else if ($current === '\\') {
                            $subValue .= $this->unicode($string, $i);
                        } else if ($current === ';' || $pn) {
                            $status = array_pop($from);
                        } else if ($current !== '}') {
                            $subValue .= $current;
                        }

                        if (($current === '}' || $current === ';' || $pn) && !empty($selector)) {
                            $property = strtolower($property);

                            if (($trimmed = trim($subValue, self::$whitespace)) !== '') {
                                $subValues[] = $trimmed;
                                $subValue = '';
                            }

                            $valid = $this->propertyIsValid($property);
                            if ($valid || !$this->discardInvalidProperties) {
                                end($stack)->addProperty(new Element\Property($property, $subValues, $this->line));
                            } else {
                                $this->logger->log("Removed invalid property: $property", Logger::WARNING, $this->line);
                            }
                            if (!$valid && !$this->discardInvalidProperties) {
                                $this->logger->log(
                                    "Invalid property in {$this->cssLevel}: $property",
                                    Logger::WARNING,
                                    $this->line
                                );
                            }

                            $property = $value = '';
                            $subValues = array();
                        }
                        if ($current === '}') {
                            array_pop($stack);

                            array_pop($from);
                            $status = array_pop($from);
                            $selector = '';
                        }
                    } else if (!$pn) {
                        $last = strcspn($string, self::$tokensList . self::$whitespace, $i);
                        if ($last !== 0) {
                            $subValue .= substr($string, $i, $last);
                            $i += $last - 1;
                        } else if (ctype_space($current)) {
                            if (($trimmed = trim($subValue, self::$whitespace)) !== '') {
                                $subValues[] = $trimmed;
                                $subValue = '';
                            }
                        } else {
                            $subValue .= $current;
                        }
                    }
                    break;

                /* Case data in bracket */
                case 'inbrck':
                    if (strpos("\"'() ,\n" . self::$whitespace, $current) !== false && !self::escaped($string, $i)) {
                        if (($current === '"' || $current === '\'') && !self::escaped($string, $i)) {
                            $status = 'instr';
                            $from[] = 'inbrck';
                            $currentString = $stringEndsWith = $current;
                            continue;
                        } else if ($current === '(') {
                            ++$bracketCount;
                        } else if ($current === ')' && --$bracketCount === 0) {
                            $status = array_pop($from); // Go back to prev parser
                        } else if ($current === "\n") {
                            $current = ' '; // Change new line character to normal space
                        }

                        if (
                            strpos(self::$whitespace, $current) !== false &&
                            in_array(substr($subValue, -1), array(' ', ',', '('))
                        ) {
                            continue; // Remove multiple spaces and space after token
                        } else if (($current === ',' || $current === ')') && substr($subValue, -1) === ' ') {
                            $subValue = substr($subValue, 0, -1); // Remove space before ',' or ')'
                        }
                    }

                    $subValue .= $current;
                    break;

                /* Case in string */
                case 'instr':
                    // ...and no not-escaped backslash at the previous position
                    if ($current === "\n" && !($string{$i - 1} === '\\' && !self::escaped($string, $i - 1))) {
                        $current = "\\A ";
                        $this->logger->log('Fixed incorrect newline in string', Logger::WARNING, $this->line);
                    }

                    $currentString .= $current;

                    if ($current === $stringEndsWith && !self::escaped($string, $i)) {
                        $currentString = self::normalizeQuotes($currentString);

                        $status = array_pop($from);
                        if ($status === 'is') {
                            $selector .= $currentString;
                        } else {
                            $subValue .= $currentString;
                        }
                    }
                    break;

                /* Case in at rule */
                case 'at':
                    if ($this->isToken($string, $i)) {
                        if ($current === '"' || $current === '\'') {
                            $status = 'instr';
                            $from[] = 'at';
                            $currentString = $stringEndsWith = $current;
                        } else if ($current === '(') {
                            $subValue .= $current;
                            $status = 'inbrck';
                            $bracketCount = 1;
                            $from[] = 'at';
                        } else if ($current === ';') {
                            $subValues[] = $subValue;
                            $this->processAtRule($subValues, $stack);
                            $subValues = array();
                            $subValue = '';
                            $status = 'is';
                        } else if ($current === ',') {
                            $subValues[] = $subValue;
                            $subValues[] = ',';
                            $subValue = '';
                        } else if ($current === '{') {
                            if (trim($subValue) !== '') {
                                $subValues[] = $subValue;
                            }

                            $status = $this->nextParserInAtRule($string, $i);
                            if ($status === 'ip') {
                                $selector = ' ';
                            }
                            $from[] = 'is';

                            $stack[] = end($stack)->addBlock(new Element\AtBlock($subValues));

                            $subValues = array();
                            $subValue = '';
                        } else if ($current === '/' && $string{$i + 1} === '*') {
                            end($stack)->addComment(new Comment($this->parseComment($string, $i)));
                        } else if ($current === '\\') {
                            $subValue .= $this->unicode($string, $i);
                        } else {
                            $subValue .= $current;
                        }
                    } else if (ctype_space($current)) {
                        if (trim($subValue) !== '') {
                            $subValues[] = $subValue;
                            $subValue = '';
                        }
                    } else {
                        $subValue .= $current;
                    }
                    break;
            }
        }

        return $root;
    }

    /**
     * @todo Refactor
     * @param Element\Selector $selector
     * @param array $selectorSeparate
     */
    protected function setSubSelectors(Element\Selector $selector, array $selectorSeparate)
    {
        $lastPosition = 0;
        $selectorSeparate[] = strlen($selector->getName());

        $lastSelectorSeparateKey = count($selectorSeparate) - 1;
        foreach ($selectorSeparate as $num => $pos) {
            if ($num === $lastSelectorSeparateKey) {
                ++$pos;
            }

            $selector->subSelectors[] = substr($selector->getName(), $lastPosition, $pos - $lastPosition - 1);
            $lastPosition = $pos;
        }
    }

    /**
     * @param string $string
     * @param int $i
     * @return string
     */
    protected function parseComment($string, &$i)
    {
        $i += 2; // Skip /* characters
        $commentLength = strpos($string, '*/', $i);

        // Comment end not exists, rest of string is inside comment
        $commentLength = $commentLength !== false  ? $commentLength - $i :  strlen($string) - $i - 1;

        if ($commentLength > 0) {
            $this->line += substr_count($string, "\n", $i, $commentLength); // Count new lines inside comment
            $comment = substr($string, $i, $commentLength);
        } else {
            $comment = '';
        }

        $i += $commentLength + 1; // Continue outside of */
        return $comment;
    }

    /**
     * Process charset, namespace or import at rule
     * @param array $subValues
     * @param array $stack
     */
    protected function processAtRule(array $subValues, array $stack)
    {
        /** @var Element\Root $parsed */
        $parsed = $stack[0];
        $rule = strtolower(array_shift($subValues));

        switch ($rule) {
            case 'charset':
                if (!empty($parsed->charset)) {
                   $this->logger->log("Only one @charset may be in document, previous is ignored",
                       Logger::WARNING,
                       $this->line
                   );
                }

                $parsed->charset = $subValues[0];

                if (!empty($parsed->elements) || !empty($parsed->import) || !empty($parsed->namespace)) {
                    $this->logger->log("@charset must be before anything", Logger::WARNING, $this->line);
                }
                break;

            case 'namespace':
                if (isset($subValues[1])) {
                    $subValues[0] = ' ' . $subValues[0];
                }

                $parsed->namespace[] = new Element\LineAt($rule, $subValues);
                if (!empty($parsed->elements)) {
                    $this->logger->log("@namespace must be before selectors", Logger::WARNING, $this->line);
                }
                break;

            case 'import':
                $parsed->import[] = new Element\LineAt($rule, $subValues);
                if (!empty($parsed->elements)) {
                    $this->logger->log("@import must be before anything selectors", Logger::WARNING, $this->line);
                } else if (isset($stack[1])) {
                    $this->logger->log("@import cannot be inside @media", Logger::WARNING, $this->line);
                }
                break;

            default:
                $lineAt = new Element\LineAt($rule, $subValues);
                end($stack)->addLineAt($lineAt);
                break;
        }
    }

    /**
     * @param string $string
     * @param int $i
     * @return string Parser section name
    */
    protected function nextParserInAtRule($string, $i)
    {
        ++$i;
        $nextColon = strpos($string, ':', $i);

        if ($nextColon === false) {
            return 'is';
        }

        $nextCurlyBracket = strpos($string, '{', $i);

        if ($nextCurlyBracket === false) {
            return 'ip';
        }

        while (self::escaped($string, $nextColon)) {
            $nextColon = strpos($string, ':', $nextColon);
        }

        while (self::escaped($string, $nextCurlyBracket)) {
            $nextCurlyBracket = strpos($string, '{', $i);
        }

        return $nextColon > $nextCurlyBracket ? 'is' : 'ip';
    }

    /**
     * Parse unicode notations and find a replacement character
     * @param string $string
     * @param integer $i
     * @return string
     */
    protected function unicode($string, &$i)
    {
        ++$i;
        $add = '';
        $replaced = false;

        while (isset($string{$i}) && (ctype_xdigit($string{$i}) || ctype_space($string{$i})) && !isset($add{6})) {
            $add .= $string{$i};

            if (ctype_space($string{$i})) {
                break;
            }
            $i++;
        }

        $decAdd = hexdec($add);
        if ($decAdd > 47 && $decAdd < 58 || $decAdd > 64 && $decAdd < 91 || $decAdd > 96 && $decAdd < 123) {
            $this->logger->log(
                "Replaced unicode notation: Changed \\$add to " . chr($decAdd),
                Logger::INFORMATION,
                $this->line
            );
            $add = chr($decAdd);
            $replaced = true;
        } else {
            $add = $add === ' ' ? '\\' . $add : trim('\\' . $add);
        }

        if (isset($string{$i + 1}) && ctype_xdigit($string{$i + 1}) && ctype_space($string{$i})
                        && !$replaced || !ctype_space($string{$i})) {
            $i--;
        }

        if ($add !== '\\' || !$this->removeBackSlash || strpos(self::$tokensList, $string{$i + 1}) !== false) {
            return $add;
        }

        if ($add === '\\') {
            $this->logger->log('Removed unnecessary backslash', Logger::INFORMATION, $this->line);
        }
        return '';
    }

    /**
     * Checks if the next word in a string from pos is a CSS property
     * @param string $string
     * @param integer $pos
     * @return bool
     * @access private
     * @version 1.2
     */
    protected function propertyIsNext($string, $pos)
    {
        $string = substr($string, $pos);
        $string = strstr($string, ':', true);

        if ($string === false) {
            return false;
        }

        $string = strtolower(trim($string));

        if (isset(self::$allProperties[$string])) {
            $this->logger->log('Added semicolon to the end of declaration', Logger::WARNING, $this->line);
            return true;
        }

        return false;
    }

    /**
     * Checks if there is a token at the current position
     * @param string $string
     * @param integer $i
     * @return bool
     */
    protected function isToken($string, $i)
    {
        return (strpos(self::$tokensList, $string{$i}) !== false && !self::escaped($string, $i));
    }

    /**
     * Checks if a property is valid
     * @param string $property
     * @return bool;
     * @access public
     * @version 1.0
     */
    protected function propertyIsValid($property)
    {
        return (isset(self::$allProperties[$property]) &&
            strpos(self::$allProperties[$property], $this->cssLevel) !== false);
    }

    /**
     * Checks if a character is escaped (and returns true if it is)
     * @param string $string
     * @param integer $pos
     * @return bool
     */
    public static function escaped($string, $pos)
    {
        return !((!isset($string{$pos - 1}) || $string{$pos - 1} !== '\\') || self::escaped($string, $pos - 1));
    }

    /**
     * Convert all possible single quote to double quote
     * @param string $string
     * @return string
     */
    public static function normalizeQuotes($string)
    {
        if (strpos($string, '"') === false) {
            return '"' . substr($string, 1, -1) . '"';
        }

        return $string;
    }

     /**
     * Explodes a string as explode() does, however, not if $sep is escaped or within a string.
     * @param string $sep separator
     * @param string $string
     * @return array
     */
    public static function explodeWithoutString($sep, $string)
    {
        if ($string === '' || $string === $sep) {
            return array();
        }

        $insideString = false;
        $to = '';
        $output = array(0 => '');
        $num = 0;

        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            if ($insideString) {
                if ($string{$i} === $to && !self::escaped($string, $i)) {
                    $insideString = false;
                }
            } else {
                if ($string{$i} === $sep && !self::escaped($string, $i)) {
                    ++$num;
                    $output[$num] = '';
                    continue;
                } else if ($string{$i} === '"' || $string{$i} === '\'' || $string{$i} === '(' && !self::escaped($string, $i)) {
                    $insideString = true;
                    $to = ($string{$i} === '(') ? ')' : $string{$i};
                }
            }

            $output[$num] .= $string{$i};
        }

        return $output;
    }
}