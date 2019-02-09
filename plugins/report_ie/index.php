<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 
# функция автоподключения плагина
function report_ie_autoload()
{
	mso_hook_add( 'head', 'report_ie_custom');
}

# функции плагина
function report_ie_custom($text)  
{
	global $MSO;
	$url = $MSO->config['plugins_url'] . 'report_ie/';
	echo <<<EOF
	<!--[if lte IE 6]> <script src="{$url}report_ie.js" type="text/javascript"></script> <![endif]-->
EOF;
}

?>