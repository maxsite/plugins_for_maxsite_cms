<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


# функция автоподключения плагина
function linkator_autoload($args = array()){
	mso_hook_add('content','linkator_hook_function'); # хук на вывод контента
}

# функции плагина
function linkator_hook_function($markup = ''){

	return preg_replace_callback('/\[\[(.+?)\]\]/','linkator_create_links',$markup);
} 

function linkator_create_links($matches){
	$val = explode('|',$matches[1]);
	if( count($val) == 2 ){
		return '<a href="'. getinfo('siteurl') .'page/'. mso_slug($val[0]) .'/">' . $val[1] . '</a>' ;
	}
	return '<a href="'. getinfo('siteurl') .'page/'. mso_slug($val[0]) .'/">' . $val[0] . '</a>' ;
}

?>