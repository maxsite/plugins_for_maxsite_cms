<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function ctrl_pagination_autoload($args = array())
{
	mso_hook_add( 'head', 'ctrl_pagination_head');
	mso_hook_add( 'pagination', 'ctrl_pagination_arrows', 1);
}


function ctrl_pagination_head ($text) 
{
	global $MSO;
	echo '<script type="text/javascript" src="'. getinfo('plugins_url') .'ctrl_pagination/navigate.js"></script>';
	$next_url = mso_url_paged_inc($max = false, $inc = 1, $empty_no_range = true, $url = '', $min = 1, $next = 'next');
	if (is_type('home')) { 
		echo '<link rel="next" href="' . $next_url . '" id="NextLink" />'; 
		if ( mso_segment(2) == 'next') {
			if ( mso_segment(3) == '2' ) {
				$prev_url = getinfo('siteurl');
			}
			else {
				$prev_url = mso_url_paged_inc($max = false, $inc = -1, $empty_no_range = true, $url = '', $min = 1, $next = 'next');
			}
			echo '<link rel="prev" href="' . $prev_url . '" id="PrevLink" />'; 
		}
	}
}
function ctrl_pagination_arrows ($text) {
	echo '<center>← + Ctrl + →</center>';
}

?>
