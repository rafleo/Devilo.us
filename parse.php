<?php
header('Content-Type:text/html; charset=utf-8');

require_once __DIR__ . '/lib/CSSTidy.php';

$processed = false;
$inputError = false;
$file_ok = false; // if output to file has been selected and file has been generated, this will be set to true
$url = (isset($_POST['url']) && !empty($_POST['url']) && $_POST['url']!='http://') ? $_POST['url'] : false;



/* ------------- Preparation & Sanitation ------------ */

if (get_magic_quotes_gpc()) {
	if (isset($_POST['css_text'])) {
		$_POST['css_text'] = stripslashes($_POST['css_text']);
	}
	if (isset($_POST['custom'])) {
		$_POST['custom'] = stripslashes($_POST['custom']);
	}
	if (isset($_COOKIE['custom_template'])) {
		$_COOKIE['custom_template'] = stripslashes($_COOKIE['custom_template']);
	}
}


function rmdirr($dirname,$oc=0)
{
	// Sanity check
	if (!file_exists($dirname)) {
	  return false;
	}
	// Simple delete for a file
	if (is_file($dirname) && (time()-fileatime($dirname))>3600) {
	   return unlink($dirname);
	}
	// Loop through the folder
	if(is_dir($dirname))
	{
	$dir = dir($dirname);
	while (false !== $entry = $dir->read()) {
	   // Skip pointers
	   if ($entry === '.' || $entry === '..') {
		   continue;
	   }
	   // Recurse
	   rmdirr($dirname.'/'.$entry,$oc);
	}
	$dir->close();
	}
	// Clean up
	if ($oc==1)
	{
		return rmdir($dirname);
	}
}



/* ------------- Settings & Processing ------------ */


if (isset($_POST['css_text']) || $url) {
    $processed = true;
    $inputCss = $_POST['css_text'];

    $cssTidy = new \CSSTidy\CSSTidy;
	
	$is_custom = isset($_POST['custom']) && !empty($_POST['custom']) && isset($_POST['template']) && ($_POST['template'] === 'custom');
	if($is_custom) {
		setcookie ('custom_template', $_POST['custom'], time()+360000);
		$cssTidy->configuration->setTemplate($_POST['custom']);
	}
	
	if($_POST['template'] == 'low') $cssTidy->configuration->loadPredefinedTemplate(\CSSTidy\Configuration::LOW_COMPRESSION);
	else if($_POST['template'] == 'standard') $cssTidy->configuration->loadPredefinedTemplate(\CSSTidy\Configuration::STANDARD_COMPRESSION);
	else if($_POST['template'] == 'high') $cssTidy->configuration->loadPredefinedTemplate(\CSSTidy\Configuration::HIGH_COMPRESSION);
	else $cssTidy->configuration->loadPredefinedTemplate(\CSSTidy\Configuration::HIGHEST_COMPRESSION);
		
	// The default options (see Configuration.php) do not need to be checked - in case no parameters submitted, CSSTidy will revert to them
    if($_POST['css_level'] == '10') $cssTidy->configuration->setCssLevel(\CSSTidy\Configuration::CSS1_0);
    else if($_POST['css_level'] == '20') $cssTidy->configuration->setCssLevel(\CSSTidy\Configuration::CSS2_0);
    else if($_POST['css_level'] == '21') $cssTidy->configuration->setCssLevel(\CSSTidy\Configuration::CSS2_1);
    else if($_POST['css_level'] == '30') $cssTidy->configuration->setCssLevel(\CSSTidy\Configuration::CSS3_0);
	
    if($_POST['merge_selectors'] == '1') $cssTidy->configuration->setMergeSelectors(\CSSTidy\Configuration::SEPARATE_SELECTORS);
    else if($_POST['merge_selectors'] == '2') $cssTidy->configuration->setMergeSelectors(\CSSTidy\Configuration::MERGE_SELECTORS);
	
    if($_POST['optimise_shorthands'] == '0') $cssTidy->configuration->setOptimiseShorthands(\CSSTidy\Configuration::NOTHING);
    else if($_POST['optimise_shorthands'] == '2') $cssTidy->configuration->setOptimiseShorthands(\CSSTidy\Configuration::FONT);
    else if($_POST['optimise_shorthands'] == '3') $cssTidy->configuration->setOptimiseShorthands(\CSSTidy\Configuration::BACKGROUND);
	
    if($_POST['case_properties'] == '2') $cssTidy->configuration->setCaseProperties(\CSSTidy\Configuration::UPPERCASE);
	if(!isset($_POST['remove_last_sem']) && isset($_POST['post'])) $cssTidy->configuration->setRemoveLastSemicolon(false);
	if(isset($_POST['lowercase'])) $cssTidy->configuration->setLowerCaseSelectors(true);
	if(!isset($_POST['compress_c']) && isset($_POST['post'])) $cssTidy->configuration->setCompressColors(false);
	if(!isset($_POST['compress_fw']) && isset($_POST['post'])) $cssTidy->configuration->setCompressFontWeight(false);
	if(!isset($_POST['rbs']) && isset($_POST['post'])) $cssTidy->configuration->setRemoveBackSlash(false);
	if(isset($_POST['preserve_css'])) $cssTidy->configuration->setPreserveComments(true);
	if(isset($_POST['sort_sel'])) $cssTidy->configuration->setSortSelectors(true);
	if(isset($_POST['sort_de'])) $cssTidy->configuration->setSortProperties(true);
	if(isset($_POST['discard'])) $cssTidy->configuration->setDiscardInvalidProperties(true);
	if(isset($_POST['timestamp'])) $cssTidy->configuration->setAddTimestamp(true);
	
	
	//$cssTidy->configuration->setDiscardInvalidSelectors();
    //$cssTidy->configuration->setConvertUnit();
	

	if($url)  // validate URL, then fetch content
	{
		if(substr($_POST['url'],0,7) != 'http://') {
			$_POST['url'] = 'http://'.$_POST['url'];
		}
		
		ini_set('user_agent', 'Devilo.us CSS parser v2');
		$inputCss = @file_get_contents($url);
	}
	
	

    try {
        $output = $cssTidy->process($inputCss);
        $inputError = false;
    } 
	catch (\Exception $e) {
        $inputError = true;
		// echo $e; // Debugger
    }
}



/* ------------- Output Results ------------ */


if ($processed) {
	if ($inputError) {
		echo '<div class="message error">No or invalid CSS input!</div>';
	}
	else {
		$ratio = $output->getRatio();
		$diff = $output->getDiff();
		if($ratio>0) $ratio = '<span style="color: #0c0;">'.$ratio.'%</span></strong> ('.$diff.' Bytes)';
		else $ratio = '<span style="color: #c00;">'.$ratio.'%</span></strong> ('.$diff.' Bytes)';
		
		if(isset($_POST['file_output']))
		{
			/// rmdirr('temp'); // empty previous files (only when requested to create new one, to decrease server load  -- Unfortunately deletes download script as well -.-
			
			$filename = md5(mt_rand().time().mt_rand());
			$handle = fopen('temp/'.$filename.'.css','w');
			if($handle) {
				if(fwrite($handle,$output->plain()))
				{
					$file_ok = true;
				}
			}
			fclose($handle);
		}
		
		if($cssTidy->logger->getMessages() > 0): ?>
        <br /><br /><p>Parser Log</p>
        <fieldset id="messages">
            <div id="logWrapper"><dl>
			<?php foreach ($cssTidy->logger->getMessages() as $line => $messages): ?>
                <dt><?php echo $line ?></dt>
                <?php foreach ($messages as $message): ?>
                    <?php switch ($message[\CSSTidy\Logger::TYPE]) {
                        case \CSSTidy\Logger::INFORMATION:
                            echo '<dd class="information">';
                            break;
                        case \CSSTidy\Logger::WARNING:
                            echo '<dd class="warning">';
                            break;
                        case \CSSTidy\Logger::ERROR:
                            echo '<dd class="error">';
                    }
                    echo $message[\CSSTidy\Logger::MESSAGE] ?>
                    </dd>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </dl></div>
        </fieldset>
        <?php endif;
		
		
		if($file_ok) // ----> Output as CSS file
		{
			echo '<br /><br />';
			echo '<div id="downloadFile"><a href="temp/?f='.$filename.'">Download CSS file</a></div>';
			echo '<p class="fileResults">Input: '.$output->size(\CSSTidy\Output::INPUT).' kB, Output: '.$output->size(\CSSTidy\Output::OUTPUT).' kB<br /><strong>Compression ratio: '.$ratio.'</p><br />';
		}
		else // ------------> Output Code
		{
			echo '<p class="compressionResults">Input: '.$output->size(\CSSTidy\Output::INPUT).' kB, Output: '.$output->size(\CSSTidy\Output::OUTPUT).' kB &nbsp;&mdash;&nbsp; <strong>Compression ratio: '.$ratio.'</p>';
			
			echo '<br /><p class="right"><a href="javascript:ClipBoard()">copy to clipboard</a></p>';
			echo '<fieldset id="convertedCode">';
			echo '<div><pre><code id="copytext">';
			echo $output->formatted();
			echo '</code></pre></div>';
			echo '</fieldset>';
			echo '<p class="right"><a href="javascript:ClipBoard()">copy to clipboard</a></p>';
	
			echo '<br /><br /><p><a href="#" onClick="$.smoothScroll({offset: -100});return false;">&#8593; Go back up</a></p>';
		}
	}
	
}

?>