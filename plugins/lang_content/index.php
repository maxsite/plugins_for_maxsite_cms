<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function lang_content_autoload() 
{
	mso_hook_add( 'content', 'lang_content_parse'); # хук на админку
}

/*function lang_content_check ($m) 
*{
*	if ( is_login() || is_login_comuser() ) 
*	{
*		return $m[1];
*	} 
*	else 
*	{
*		return 'Запись только для зарегистрированных';
*	}
} */

function en_content_check ($m)
{
	if ($m == '~\[en\](.*?)\[\/en\]~si') {
		return $m[1];
	} 
	else 
	{
		return '';
	}
}

function de_content_check ($m)
{
	if ($m == '~\[de\](.*?)\[\/de\]~si') {
		return $m[1];
	} 
	else 
	{
		return '';
	}
}

function ru_content_check ($m)
{
	if ($m == '~\[ru\](.*?)\[\/ru\]~si') {
  		return $m[1];
	} else { 
  		return '';
	}
}

function ro_content_check ($m)
{
	if ($m == '~\[ro\](.*?)\[\/ro\]~si') {
  		return $m[1];
	} else { 
  		return '';
	}
}

function ua_content_check ($m)
{
	if ($m == '~\[ua\](.*?)\[\/ua\]~si') {
  		return $m[1];
	} else { 
  		return '';
	}
}

function lang_content_parse($text) 
{   		
	$ck = mso_get_cookie('lang',NULL);
	if ($ck == 'ru') {    	
	$preg = '~\[en\](.*?)\[\/en\]~si';
	$text = preg_replace_callback($preg, "en_content_check" , $text);
	$preg = '~\[de\](.*?)\[\/de\]~si';
	$text = preg_replace_callback($preg, "de_content_check" , $text);
	$preg = '~\[ua\](.*?)\[\/ua\]~si';
	$text = preg_replace_callback($preg, "ua_content_check" , $text);
	$preg = '~\[ro\](.*?)\[\/ro\]~si';
	$text = preg_replace_callback($preg, "ro_content_check" , $text);
	$text = str_ireplace('[ru]', '', $text);
	$text = str_ireplace('[/ru]', '', $text);
		return $text;
	} elseif ($ck == 'ua') {    	
	$preg = '~\[ru\](.*?)\[\/ru\]~si';
	$text = preg_replace_callback($preg, "ru_content_check" , $text);
	$preg = '~\[en\](.*?)\[\/en\]~si';
	$text = preg_replace_callback($preg, "en_content_check" , $text);
	$preg = '~\[de\](.*?)\[\/de\]~si';
	$text = preg_replace_callback($preg, "de_content_check" , $text);
	$preg = '~\[ro\](.*?)\[\/ro\]~si';
	$text = preg_replace_callback($preg, "ro_content_check" , $text);
	$text = str_ireplace('[ua]', '', $text);
	$text = str_ireplace('[/ua]', '', $text);
	return $text;
	} elseif ($ck == 'en') {    	
	$preg = '~\[ru\](.*?)\[\/ru\]~si';
	$text = preg_replace_callback($preg, "ru_content_check" , $text);
	$preg = '~\[de\](.*?)\[\/de\]~si';
	$text = preg_replace_callback($preg, "de_content_check" , $text);
	$preg = '~\[ua\](.*?)\[\/ua\]~si';
	$text = preg_replace_callback($preg, "ua_content_check" , $text);
	$preg = '~\[ro\](.*?)\[\/ro\]~si';
	$text = preg_replace_callback($preg, "ro_content_check" , $text);
	$text = str_ireplace('[en]', '', $text);
	$text = str_ireplace('[/en]', '', $text);
		return $text;
	} elseif ($ck == 'de') {    	
	$preg = '~\[ru\](.*?)\[\/ru\]~si';
	$text = preg_replace_callback($preg, "ru_content_check" , $text);
	$preg = '~\[en\](.*?)\[\/en\]~si';
	$text = preg_replace_callback($preg, "en_content_check" , $text);
	$preg = '~\[ua\](.*?)\[\/ua\]~si';
	$text = preg_replace_callback($preg, "ua_content_check" , $text);
	$preg = '~\[ro\](.*?)\[\/ro\]~si';
	$text = preg_replace_callback($preg, "ro_content_check" , $text);
	$text = str_ireplace('[de]', '', $text);
	$text = str_ireplace('[/de]', '', $text);
	return $text;
	} elseif ($ck == 'ro') {    	
	$preg = '~\[ru\](.*?)\[\/ru\]~si';
	$text = preg_replace_callback($preg, "ru_content_check" , $text);
	$preg = '~\[en\](.*?)\[\/en\]~si';
	$text = preg_replace_callback($preg, "en_content_check" , $text);
	$preg = '~\[ua\](.*?)\[\/ua\]~si';
	$text = preg_replace_callback($preg, "ua_content_check" , $text);
	$preg = '~\[de\](.*?)\[\/de\]~si';
	$text = preg_replace_callback($preg, "de_content_check" , $text);
	$text = str_ireplace('[ro]', '', $text);
	$text = str_ireplace('[/ro]', '', $text);
	return $text;
	} else {     
		return '';          
	}

}

# end file
