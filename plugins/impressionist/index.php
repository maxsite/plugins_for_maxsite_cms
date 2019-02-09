<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function impressionist_autoload($args = array())
{
	mso_hook_add( 'head', 'impressionist_head');
	mso_hook_add( 'content', 'impressionist_content');
}

# функции плагина
function impressionist_head($arg = array())
{
	static $impressionist_js = false;
	
	if (!$impressionist_js)
		echo '	<script src="' . getinfo('plugins_url') . 'impressionist/js/mediaelement-and-player.min.js"></script>
				<link  rel="stylesheet" href="' . getinfo('plugins_url') . 'impressionist/css/style.css"  media="screen">';
		
	
	$impressionist_js = true;
	
	return $arg;
}

# callback функция 
function impressionist_content_callback($matches)
{	
	$url = $matches[1];
	//$id = md5($url);
	
	$out = '<video width="640" height="268">
		<source src="' . $url . '" type="video/mp4">
	</video>

	<script>
	$(document).ready(function() {
		$(\'video\').mediaelementplayer({
			alwaysShowControls: false,
			videoVolume: \'horizontal\',
			features: [\'playpause\',\'progress\',\'volume\',\'fullscreen\']
		});
	});
	</script>';

	return $out;
}


# функции плагина
function impressionist_content($text = '')
{
	
	$text = preg_replace_callback('~\[video=(.*?)\]~si', 'impressionist_content_callback', $text);

	return $text;
}

?>