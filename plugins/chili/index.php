<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed'); 

/**
 * Chili code highlighter plugin for MaxSite CMS
 * (c) Oleg Stadnik (aka lokee)
 * 			http://lokee.rv.ua
 */

function chili_autoload($args = array())
{
	mso_hook_add( 'content_out', 'chili_content' );
	mso_hook_add( 'head', 'chili_template_head' );
}

function chili_template_head( $arg = array() )
{
	echo "\n\t<script type=\"text/javaScript\" src=\"" . getinfo('plugins_url') . "chili/chili-js/jquery.chili-2.2.js\"></script>";
	echo "\n\t<script type=\"text/javascript\">
	\n\t\tChiliBook.recipeFolder = \"" . getinfo('plugins_url') . "chili/chili-js/\";
	\n\t\tChiliBook.lineNumbers = true;
	\n\t</script>";
	return $arg;
}

function chili_content_callback( $matches )
{
	$code = $matches[2];
	// hack
	$del = array( "<p>" => "", "</p>" => "" );
	$code = strtr( $code, $del );
	// end hack
	$code = trim( htmlspecialchars( htmlspecialchars_decode( $code ) ) );

	return '<code class="' . $matches[1] . '">' . $code . '</code>';
}

function chili_content( $text )
{
	// html code
	$pattern = '|<code lang="(.*?)">(.*?)</code>|si';
	$text = preg_replace_callback($pattern, 'chili_content_callback', $text);
	// bb code
	$pattern = '|\[code lang="(.*?)"\](.*?)\[/code\]|si';
	$text = preg_replace_callback($pattern, 'chili_content_callback', $text);

	return $text;
}

?>