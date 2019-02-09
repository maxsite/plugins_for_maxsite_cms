<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * vim_syntaxer
 * (c) http://vizr.ru/
 */


# функция автоподключения плагина
function vim_syntaxer_autoload($args = array())
{
	mso_hook_add( 'head', 'vim_syntaxer_head');
	mso_hook_add( 'content', 'vim_syntaxer_go');
}


function vim_syntaxer_head($text) 
{
	global $MSO;
	$url = $MSO->config['plugins_url'] . 'vim_syntaxer/';
	echo <<<EOF
	<script type="text/javascript" src="{$url}scripts/shCore.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushCss.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushDelphi.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushJScript.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushPhp.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushPython.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushPlain.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushSql.js"></script>
	<script type="text/javascript" src="{$url}scripts/shBrushXml.js"></script>
	<link type="text/css" rel="stylesheet" href="{$url}styles/shCore.css"/>
	<link type="text/css" rel="stylesheet" href="{$url}styles/shThemeDefault.css"/>
	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = '{$url}scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>
EOF;
}
function vim_syntaxer_go($code)
{
		return $code;
}


?>
