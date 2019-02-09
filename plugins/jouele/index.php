<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://max-3000.com/
 */


# функция автоподключения плагина
function jouele_autoload($args = array())
{
	mso_hook_add( 'head', 'jouele_head');
	mso_hook_add( 'admin_head', 'jouele_head');
	mso_hook_add( 'content', 'jouele_content'); # хук на вывод контента после обработки всех тэгов
}

function jouele_head($args = array()) 
{
	echo mso_load_jquery();
	
	$url = getinfo('plugins_url') . 'jouele/';
	
	
	echo <<<EOF
	<link rel="stylesheet" href="{$url}static/jouele.css" type="text/css" media="screen">
	<link rel="jouele-swf-object" href="{$url}static/jplayer.swf">
	<script src="${url}static/jquery.jplayer.min.js"></script>
	<script src="${url}static/jouele.js"></script>
EOF;

}

function jouele_content($text = '')
{
	return preg_replace_callback('~\[audio=(.*?)\]~si', 'jouele_content_callback', $text); 
}

function jouele_content_callback ($matches) {
    $url = $matches[1];
    return '<div style=""width: 500px; height: 200px; padding: 20px">
    <a href="'.$url.'" class="jouele"></a>
</div>
';
}
# end file
