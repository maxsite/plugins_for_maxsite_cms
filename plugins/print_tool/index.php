<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Name: Print Tool (The Printliminator)
 * Authors: Tux(http://6log.ru/), Chris Coyier(http://css-tricks.com/)
 * Plugin URL: http://6log.ru/print_tool
 */


# функция автоподключения плагина
function print_tool_autoload()
{	
	if ( is_type('page') )
	{
		mso_hook_add( 'content_start', 'print_tool_custom');
	}
}

# функции плагина
function print_tool_custom($arg = array())
{

echo "\n<div class=\"print\"><a href=\"
javascript:(function(){function%20loadScript(a,b){var%20c=document.createElement('script');c.type='text/javascript';c.src=a;var%20d=document.getElementsByTagName('head')[0],done=false;c.onload=c.onreadystatechange=function(){if(!done&&(!this.readyState||this.readyState=='loaded'||this.readyState=='complete')){done=true;b()}};d.appendChild(c)}loadScript('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js?ver=1.3.2',function(){loadScript('http://css-tricks.com/examples/ThePrintliminator/js/printliminator.js',function(){printlimator()})})})() \" id=\"print\" target=\"_self\" title=\"В текущем окне\">".t('Версия для печати')."</a></div>";
	
	return $arg;	
}

?>