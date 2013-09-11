<?php if(isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {if(extension_loaded('zlib')){if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler");}} ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Devilo.us compression and optimization for your CSS3 code!</title>
<meta name="keywords" content="CSS, CSS3, optimize, optimization, compress, compression, tidy, tidy up, style, cascading style sheets, CSS 3.0, CSS 3, dev, web, html, code" />
<meta name="description" content="Compress and optimize your CSS code with Devilo.us. Also works with CSS3!" />
<link href="js/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="image_src" href="img/birdie.png" />
<?php flush(); ?>
</head>
<body id="index" class="home">

<header id="banner" class="body">
	<div><span id="logo">Devilo.us</span><span id="websection">&nbsp;v2.0</span></div>
    <strong>Compress</strong> and <strong>tidy up</strong> your <strong>CSS</strong>
</header>

<section id="content" class="body">
    <div id="highbg">
      <form action="parse.php" method="POST" id="devilousCSS" onsubmit="return TrackConvert()" >
		<div class="rowElem"><label class="inputlabel">CSS from URL</label><input type="text" class="inputurl" name="url" id="url" value="http://" /></div>
        <br />&nbsp;
		<p>CSS from direct input</p>
        <textarea id="css_text" name="css_text"></textarea>
        <br />
        <div class="rowElem">
            <span class="rightf"><input type="checkbox" name="file_output" id="file_output" value="file_output" /><label>&nbsp;Output as file</label><a href="help/output-file.htm" class="jTip" id="3" name="File Output Info">?</a></span>
			<label>Compression&nbsp;</label>
			<select id="template" name="template">
				<option value="highest">Highest (no readability)</option>
				<option value="high">High</option>
				<option value="standard">Medium (balanced)</option>
				<option value="low">Low (high readability)</option>
				<!--<option value="custom">Custom...</option>-->
			</select>
		</div>
        
		<p><br /><br /><a href="#" onClick="$('#moreOptions').slideToggle('slow',function(){$('#moreOptions .jqTransformSelectWrapper').css('width','150px');$('#moreOptions .jqTransformSelectWrapper div span').css('width','150px');$('#moreOptions ul').css('width','148px');});$(this).text($(this).text() == 'Show advanced options &or;' ? 'Hide advanced options &and;' : 'Show advanced options &or;');">Show advanced options &or;</a></p>
        
        <div id="moreOptions">
        	<table width="100%" border="0">
              <tr>
                <td valign="top" width="50%">
                    <div class="rowElem"><input type="checkbox" name="compress_c" id="compress_c" checked="checked" /><label>&nbsp;Compress colours</label></div>
                    <div class="rowElem"><input type="checkbox" name="compress_fw" id="compress_fw" checked="checked" /><label>&nbsp;Compress font-weight</label></div><br />
                    <div class="rowElem"><label>Optimise shorthands</label>
                    	<select name="optimise_shorthands" id="optimise_shorthands"> 
            				<option value="0">No</option>
                            <option value="1">Yes, but common only</option>
                            <option value="2">Yes, common + fonts</option>
                            <option value="3" selected="selected">Yes, all</option>
                        </select></div>
                    <br /><br />
                    <div class="rowElem"><label>Regroup selectors</label> 
            			<select name="merge_selectors" id="merge_selectors"> 
              				<option value="0">No</option>
                            <option value="1">Seperate</option>
                            <option value="2" selected="selected">Merge</option>
                        </select><a href="help/merge-selectors.htm" class="jTip" id="1" name="Regroup Selectors Info">?</a></div>
                    <div class="rowElem"><input type="checkbox" name="sort_sel" id="sort_sel" /><label>&nbsp;Sort selectors (use with caution)</label><a href="help/sort-selectors.htm" class="jTip" id="4" name="Sort Selectors Info">?</a></div>
                    <div class="rowElem"><input type="checkbox"  name="sort_de" id="sort_de" /><label>&nbsp;Sort properties</label></div>
                    <br /><br />
                    <div class="rowElem"><input type="checkbox" name="preserve_css" id="preserve_css" /><label>&nbsp;Preserve comments</label></div>
                    <div class="rowElem"><input type="checkbox" id="timestamp" name="timestamp" /><label>&nbsp;Add timestamp</label></div>
                </td>
                <td valign="top">
                    <div class="rowElem"><input type="checkbox" name="rbs" id="rbs" checked="checked" /><label>&nbsp;Remove unnecessary backslashes</label></div>
                    <div class="rowElem"><input type="checkbox" id="remove_last_sem" name="remove_last_sem" checked="checked" /><label>&nbsp;Remove last semi-colons</label></div>
                    <div class="rowElem"><input type="checkbox" id="discard" name="discard" /><label>&nbsp;Discard invalid properties</label> 
                        <select name="css_level" id="css_level_opt">
                            <option value="99">CSS 3 compatible</option>
                            <option value="30">CSS 3</option>
                            <option value="21">CSS 2.1</option>
                            <option value="20">CSS 2.0</option>
                            <option value="10">CSS 1</option>
                        </select></div>
                  	<br /><br />
                    <div class="rowElem"><label for="case_properties">Case for properties</label>
                    	<!--<input type="radio" name="case_properties" id="none" value="0" /><label>None</label>-->
                        <input type="radio" name="case_properties" id="lower_yes" value="1" checked="checked" /><label>Lowercase</label>
                        <input type="radio" name="case_properties" id="upper_yes" value="2" /><label>Uppercase</label></div>
                    <div class="rowElem"><input type="checkbox" name="lowercase" id="lowercase" value="lowercase" /><label>&nbsp;Lowercase selectors</label><a href="help/lowercase-selectors.htm" class="jTip" id="2" name="Lowercase Conversion Info">?</a></div>
                </td>
              </tr>
            </table>
        </div>
		<div class="rowElem right"><img src="img/loading.gif" id="loading" width="32" height="32" /><input type="hidden" name="post" /><input type="submit" id="submit" value="Convert CSS" /></div>
	  </form>
      <div id="devilousCode"></div>
    </div>
    
    <div id="w3clink" onClick="validateW3C();"></div>
    <div id="css3badge"></div>
    
    
    <nav id="moreInfoBtn"><a href="#" onClick="$('#moreInfo').slideToggle('slow',function(){$.smoothScroll({scrollElement: $('body, html'),scrollTarget: '#moreInfo'});return false;});">about - privacy - legal</a></nav>
    <div id="moreInfo">
    	<hr>
    	<h1>About Devilo.us CSS</h1>
        Devilo.us CSS compression and optimization engine is based on <a href="http://csstidy.sourceforge.net/" target="_blank" rel="nofollow">csstidy 1.4</a>. It has been modified to be capable of handling CSS3.<br /><br />
    	Devilo.us is a project by<br />
        <img src="img/leonardo.jpg" width="40" height="40" class="leftf" alt="Leonardo Re" style="margin-right:7px" />Leonardo Re<br />
        <a href="http://leonardo.re" title="Rafael Leonardo Re" target="_blank">Website</a> / <a href="https://twitter.com/r_leonardo_re" target="_blank" rel="nofollow">Twitter</a> / <a href="https://www.facebook.com/Rafael.Leonardo.Re" target="_blank" rel="nofollow">Facebook</a>
        <br /><br />I would be glad if you could report any malfunctions or bugs to info (at) devilo.us
    	<h1>Privacy</h1>
        We respect your privacy!<br />Unless you decide to output your CSS as file, no information or any of your code will be stored. To provide a CSS file, we need to create and store the converted file on our server. It is stored with a random name. Your original, uncompressed code however will never be saved.<br />As most websites, we collect data via Google Analytics to get to know our visitors a bit better. <br />We use this information to analyze our traffic and optimize our website for our users and focus on the most used features.
    	<h1>Legal (Terms of Use)</h1>
        By using this website, you agree to be bound by the terms and conditions.<br />
        If you do not agree to these terms and conditions, please do not use this website. Devilo.us may change or add conditions to these terms without prior notice.<br />
        We assume no liability for forms, functionality of this service or processed code provided to you. All code is provided without any warranty, expressed or implied, as to its completeness, functionality or original effect. Please use at your own risk. We recommend always backing up your original code.
    </div>
</section>

<footer id="contentinfo" class="body">
	<p>&copy; <?php date_default_timezone_set("Europe/London"); echo date("Y"); ?> Devilo.us</p>
</footer>

<script type="text/javascript" src="js/js.js" ></script>

<div class="leftsidebar">
	<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=e4f1b388-c487-448e-9c20-b96b964a94fc&amp;type=website&amp;style=vertical&amp;post_services=email%2Cgbuzz%2Cmyspace%2Csms%2Cdelicious%2Cwindows_live%2Creddit%2Cgoogle_bmarks%2Cyahoo_bmarks%2Clinkedin%2Cybuzz%2Cmister_wong%2Cbebo%2Cblogger%2Cmixx%2Ctechnorati%2Cfriendfeed%2Cpropeller%2Cwordpress%2Cnewsvine&amp;headerfg=%23FF0000&amp;headerbg=%23450E17&amp;linkfg=%2309F&amp;headerTitle=Thank%20you%20for%20sharing%20devilous%20love!"></script>
</div>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-29297164-1']);
  function TrackConvert() { 
		_gaq.push(['_trackEvent', 'CSS', 'Convert']);
		return true; 
  } 
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>
<?php ob_flush(); ?>