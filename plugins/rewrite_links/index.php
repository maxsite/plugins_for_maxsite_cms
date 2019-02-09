<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * librarian
 * (c) http://librarian.spb.ru/
 */


function rewrite_links_autoload($args = array())
{
	$prefix='a';
	mso_hook_add( 'content', 'rewrite_links_content');
	if ( mso_segment(1) == $prefix ) 
	{
		mso_hook_add( 'init', 'rewrite_links_init');
	}
}

function rewrite_links_init($args = array())
{
	global $MSO;
	$prefix='a';
	$url = '';
	$i = 2;
	while ( mso_segment($i) == true )
	{
		if ( $i == 2)
		{
			$url = $url . base64_decode(mso_segment($i));
		} 
		else
		{
			$url = $url . '?' . base64_decode(mso_segment($i));
		}
		$i++;
	}
	header('Location: ' . $url);
	exit;
	return $args;
}

function rewrite_links_content_callback($matches)
{	
	$siteurl=getinfo('siteurl');	
	if (strpos($matches[2],$siteurl)!==0)
	{
		$prefix='a';
		$url  = base64_encode($matches[2]);
		$url  = getinfo('siteurl') . $prefix . '/' . $url;
		$out = '<noindex><a' . $matches[1] . 'href="' . $url . '"' . $matches[3] . ' rel="nofollow" target="_blank" >' . $matches[4] . '</a></noindex>';
		return $out;
	}
	else
	{
		$url  = $matches[2];
		$out = '<a' . $matches[1] . 'href="' . $url . '"' . $matches[3] . '>' . $matches[4] . '</a>';
		return $out;
	}
}

function rewrite_links_content($text = '')
{
	$pattern = '|<a(.*?)href="(.*?)"(.*?)>(.*?)</a>|ui';
	$text = preg_replace_callback($pattern, 'rewrite_links_content_callback', $text);

	return $text;
}



?>
