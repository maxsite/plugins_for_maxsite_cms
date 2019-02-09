<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Alyaxey Yaskevich
 * (c) http://yaskevich.com/
 */


# функция автоподключения плагина
function page_title_autoload($args = array())
{	
	mso_hook_add( 'text_to_html', 'page_title_do');
}

function page_title_do($title) 
{
	if (!empty ($title)) {
		if ($title[0] == '"') $title[0] = '“';
		$title = str_replace(' "', ' “', $title);
		$title = str_replace('"', '”', $title);
		$title = str_replace('-', '–',$title);
		$title = str_replace('\'', '’',$title);
	}
	return $title;
}



?>