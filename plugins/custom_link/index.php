<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function custom_link_autoload($args = array())
{
	mso_hook_add( 'content_content', 'custom_link');
	mso_hook_add( 'content', 'custom_link_remove'); // хук на вывод контента
}

// функция добавения ссылки
function custom_link($link='')
{
	global $page; // pr($page);
	
	if (!is_type('home') and !is_type('category') and !is_type('tag'))
	
		return $link;
	
	if ((strpos($page['page_content'], '[link]') === false) and (strpos($page['page_content'], '[link-gal]') === false))
		
		$link = $page['page_content'];
		
	else
	{
		if (is_type('home') or is_type('category') or is_type('tag'))
		{
			$url = getinfo('site_url') . 'page/' . $page['page_slug']; // pr($url);
			$pt[0] = '~\[(link|link-gal)\]~si';
			$pt[1] = '~\[/(link|link-gal)\]~si';
			$re[0] = '<a class="custom-link" href="' . $url . '" title="' . $page['page_title'] . '">';
			$re[1] = '</a>';
			$link = preg_replace($pt, $re, $page['page_content']);
		}
	}
	return $link;
}

// функция удаления псевдотегов
function custom_link_remove($text='')
{
	if (!is_type('page') and !is_type('search'))
	
		return $text;
	
	else
	{
		if (is_type('page'))
		{
			$pt[0] = '~\[link\]~si';
			$pt[1] = '~\[/link\]~si';
			$pt[2] = '~\[link-gal\](.*?)\[/link-gal\]~si';
		}
		if (is_type('search'))
		{
			$pt[0] = '~\[(link|link-gal)\]~si';
			$pt[1] = '~\[/(link|link-gal)\]~si';
		}
		$text = preg_replace($pt, '', $text);
	}
	return $text;
}

?>