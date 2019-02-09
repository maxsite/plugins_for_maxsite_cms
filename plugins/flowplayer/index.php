<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * 
 * Author: Nureke (made on base of Martin Laine's Audio Player Plugin)
 * Автор: Нуреке (сделан на основе плагине Audio Player от Martin Laine)
 */


# функция автоподключения плагина
function flowplayer_autoload($args = array())
{
	mso_hook_add( 'head', 'flowplayer_head');
	mso_hook_add( 'content', 'flowplayer_content');
}

# функции плагина
function flowplayer_head($arg = array())
{
	static $flowplayer_js = false;
	
	if (!$flowplayer_js)
		echo '	<script language="JavaScript" src="' . getinfo('plugins_url') . 'flowplayer/flowplayer-3.0.6.min.js"></script>';
	
	$flowplayer_js = true;
	
	return $arg;
}

# callback функция 
function flowplayer_content_callback($matches)
{	
	$url = $matches[1];
	
	$out = '<!-- start flowplayer container-->'
      . '<div class="flowplayer">'
      . '<a ' 
      .   'href="' . $url . '" '
      .   'style="display:block;width:425px;height:300px;" '
      .   'id="player"> '
      . '</a> '
      . '<script language="JavaScript">'
      .   'flowplayer("player", "' . getinfo('plugins_url') . 'flowplayer/flowplayer-3.0.7.swf", {'
      .     'clip:  {'
      .       'autoPlay: false,' 
      .       'autoBuffering: true' 
      .     '}'
      .   '});'
      . '</script>'
      . '</div>'
      . '<!-- end flowplayer containter -->';

	return $out;
}


# функции плагина
function flowplayer_content($text = '')
{

	$text = preg_replace_callback('~\[flowplayer=(.*?)\]~si', 'flowplayer_content_callback', $text);

	return $text;
}

?>