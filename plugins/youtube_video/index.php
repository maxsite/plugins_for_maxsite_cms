<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* Евгений Мирошниченко
* zhenya.webdev@gmail.com
* (c) https://modern-templates.com
*
*/
function youtube_video_autoload()
{
	mso_hook_add( 'head_css', 'youtube_video_css');
	mso_hook_add( 'content', 'youtube_video_content');
}

//CSS плдключим.
function youtube_video_css($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'youtube_video/css/youtube.css">';
	return $args;
}

function youtube_video_content_callback($matches)
{

	$u = $matches[1];
	$n = strpos($u,"v=")+2;
	$url = substr($u,$n,11);

    $out = '<div id="containingBlock"><div class="videoWrapper"><iframe src="https://www.youtube.com/embed/' . $url . '?rel=0&amp;modestbranding=1" allowfullscreen></iframe></div></div>';

    return $out;
}

function youtube_video_content($text = '')
{
	$pattern = '|\[youtube=https://www.youtube.com/watch(.*?)\]|ui';

	$text = preg_replace_callback($pattern, 'youtube_video_content_callback' , $text);

	return $text;
}