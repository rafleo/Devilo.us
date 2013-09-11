<?php
if (strtolower($_SERVER['REQUEST_METHOD']) == 'get')
{
	ob_start();
	
	$cssfile = $_GET['f']; // GET of filename without ending '.css'
	
	function sanitize($string, $force_lowercase = true, $anal = true) {
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
					   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
					   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace('/\s+/', "-", $clean);
		$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
		return ($force_lowercase) ?
			(function_exists('mb_strtolower')) ?
				mb_strtolower($clean, 'UTF-8') :
				strtolower($clean) :
			$clean;
	}
		
	$URL = './' .sanitize($cssfile) . '.css';
	
	// Gather relevent info about file
	$len = filesize($URL);
	$filename = basename($URL);
	// fix for IE catching or PHP bug issue
	header("Pragma: public");
	header("Cache-Control: no-cache");
	header("Expires: -1");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	// force download dialog
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-type: application/octet-stream");
	header("Content-Transfer-Encoding: binary");
	header("Content-Disposition: attachment; filename=\"$filename\";\n\n");
	header("Content-Length: $len;\n");
	ob_end_clean();
	@readfile($URL);
}
else // not requested with GET 
{
	header("Location: http://devilo.us");
}
?>