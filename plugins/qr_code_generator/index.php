<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function qr_code_generator_autoload($args = array())
{
	if( is_type('page') ) { 	mso_hook_add( 'content_end', 'qr_code_generator_code'); }
}

function qr_code_generator_code ($text) {
	global $MSO;
	$url = $MSO->config['site_url'] . mso_current_url();
	echo '<br /><img src="http://chart.apis.google.com/chart?chs=150x150&cht=qr&chl='. $url . '">';
}

?>
