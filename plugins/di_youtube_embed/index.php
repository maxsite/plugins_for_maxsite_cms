<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function di_youtube_embed_autoload()
{
	mso_hook_add( 'head', 'di_youtube_head');
	mso_hook_add( 'content', 'di_youtube_embed_content');
}

function di_youtube_head($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'di_youtube_embed/css/youtube.css">';
	return $args;
}

function di_youtube_embed_content_callback($matches)
{

	$u = $matches[1];
	$n = strpos($u,"v=")+2;
	$url = substr($u,$n,11);

    $out = '<div id="containingBlock"><div class="videoWrapper"><iframe src="https://www.youtube.com/embed/' . $url . '?rel=0&amp;modestbranding=1" allowfullscreen></iframe></div></div>';

    return $out;
}

function di_youtube_embed_content($text = '')
{
	$pattern = '|\[youtube=https://www.youtube.com/watch(.*?)\]|ui';

	$text = preg_replace_callback($pattern, 'di_youtube_embed_content_callback' , $text);

	return $text;
}