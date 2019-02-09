<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function print_version_autoload()
{	
	if ( is_type('page') )
	{
		mso_hook_add( 'head', 'print_version_head');
		mso_hook_add( 'content_start', 'print_version_add');
	}
}

# функции плагина
function print_version_head($arg = array())
{
	echo '<style type="text/css">@media print {.b-print, #ctrl_print {display:none;}} </style>';
	echo '<script src="'.getinfo('plugins_url').'print_version/printversion.js" type="text/javascript"></script>';
	
	return $arg;
}

# функции плагина
function print_version_add($arg = array())
{
	echo "\n<div class=\"b-print\"><a href=\"#\" id=\"printversion\" target=\"_blank\" title=\"В новом окне\">".t('Версия для печати')."</a></div>\n";
	
	return $arg;	
}

?>