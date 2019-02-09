<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



# функция автоподключения плагина
function glatex_autoload() {
	mso_hook_add( 'content', 'glatex_content');
}

function glatex_check ($m) {
	return "<img src='http://chart.apis.google.com/chart?cht=tx&chs=1x0&chf=bg,s,FFFFFF00&chco=000000&chl=$m[1]' alt='$m[1]'>";
}

function glatex_content($text) {
	$preg = '~\[latex\](.*?)\[\/latex\]~si';
	$text = preg_replace_callback($preg, "glatex_check" , $text);
	$text = str_ireplace('[latex]', '', $text);
	$text = str_ireplace('[/latex]', '', $text);
	return $text;
}


?>
