<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function rg_hide_autoload($args = array())
{
	mso_hook_add( 'content', 'rg_hide_custom',30); # хук на вывод контента	
}

function rg_hide_hide_callback($matches)
{
	$m = explode('||',$matches[1]);
	$count = count($m);

	if (($count > 2)&&is_login()) {
		return $m[2];
	}

	if (is_login_comuser()||is_login()) {
		return $m[0];
	}
	elseif ($count > 0) {
		return $m[1];
	}

	return '';
}

# функции плагина
function rg_hide_custom($text = '')
{
	$text = preg_replace_callback('~\[hide\](.*?)\[\/hide\]~si', 'rg_hide_hide_callback', $text );
	return $text;
}

# end file