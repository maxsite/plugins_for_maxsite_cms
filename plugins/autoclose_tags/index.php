<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function autoclose_tags_autoload($args = array())
{
	mso_hook_add( 'content_content', 'autoclose_tags_custom');
}


function autoclose_tags_uninstall($args = array())
{
	return $args;
}


function autoclose_tags_custom($content = '')
{
	if (is_type('page')) return $content;

	preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $content, $result);
	$openedtags = $result[1];

	preg_match_all("#</([a-z]+)>#iU", $content, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);

	if(count($closedtags) == $len_opened){
		return $content;
	}

	$openedtags = array_reverse($openedtags);
	for ($i=0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)) {
			$content .= '</'.$openedtags[$i].'>';
		} else {
			unset($closedtags[array_search($openedtags[$i],$closedtags)]);
		}
	}
	return $content;

}

?>