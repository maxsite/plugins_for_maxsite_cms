<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function snow_autoload($args = array())
{
	mso_hook_add( 'head', 'snow_head');
}

# функции плагина
function snow_head($arg = array())
{
	static $snow_js = false;
	
	if (!$snow_js)
		echo '	<script type="text/javascript">';
    echo '	sitePath = "' . getinfo('plugins_url') . '";';
    echo '	sflakesMax = 64;';
    echo '	sflakesMaxActive = 64;';
    echo '	svMaxX = 3;';
    echo '	svMaxY = 3;';
    echo '	ssnowStick = 1;';
    echo '	sfollowMouse = 1;</script>';	
		echo '	<script type="text/javascript" src="' . getinfo('plugins_url') . 'snow/snow.js"></script>';
	
	$snow_js = true;
	
	return $arg;
}
?>