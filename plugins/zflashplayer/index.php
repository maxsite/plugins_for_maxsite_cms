<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function zflashplayer_autoload($args = array())
{
	mso_hook_add( 'content_out', 'zflashplayer_go');
}

# функции плагина
function zflashplayer_go($text = ''){
	//return preg_replace('\[flash\((\d+),(\d+)\)](.*?)\[/flash\]', , $text);
	$path = getinfo('plugins_url') . 'zflashplayer/';
	
	preg_match_all("|\[flash\((\d+),(\d+)\)\](.*?)\[/flash\]|U", $text, $matches, PREG_SET_ORDER);

	foreach ($matches as $val) {
		$repl = '<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="'. $val[1] .'" height="'. $val[2] .'">
		<param name="movie" value="'. $path .'player.swf" />
		<param name="allowfullscreen" value="true" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="'. $val[3] .'&image='. $path .'preview.png" />
		<embed
			type="application/x-shockwave-flash"
			id="player2"
			name="player2"
			src="'. $path .'player.swf" 
			width="'. $val[1] .'" 
			height="'. ($val[2] + 24) .'"
			allowscriptaccess="always" 
			allowfullscreen="true"
			flashvars="file='. $val[3] .'&image='. $path .'preview.png" 
		/></object>';
		$text = str_replace($val[0], $repl, $text);
	}	
	return $text;
}


?>