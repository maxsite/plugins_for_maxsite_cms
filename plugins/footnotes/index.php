<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 * @autor Dizatorr 
 * (c) http://unicorn.e-nk.ru
 */


//Конец строки и тав определенные в доку
if ( !defined('NR') ){define ('NR',"\n");}

# функция автоподключения плагина
function footnotes_autoload($args = array())
{
	mso_hook_add( 'head', 'footnotes_head');
	mso_hook_add( 'content', 'footnotes_custom'); # хук на вывод контента
}

# функции плагина
function footnotes_custom($markup = '')
{
	$footnote_end = '';
	preg_match_all("~\(\((.*?)\)\)~si",  $markup, $footnotes);
	$footnotes[0] = array_unique($footnotes[0]);
	$i=1;
	foreach ($footnotes[0] as $key => $footnote)
	{
		$markup = str_replace ($footnote, '<sup><a href="#fn__'.$i.'" name="fnt__'.$i.'" id="fnt__'.$i.'" class="fn_top">'.$i.')</a></sup>', $markup);
		$footnote_end .= __footnote_end($i, $footnotes[1][$key]);
		++$i;
	}
	$footnote_end = '<div class="footnotes">'.NR.$footnote_end.'</div>';
	return $markup.$footnote_end;
}

function footnotes_head()
{
	echo '	<link rel="stylesheet" href="' . getinfo('plugins_url') . '/footnotes/footnotes.css" type="text/css" media="screen">' . NR;
}

function __footnote_end($i, $footnote) 
{
	$doc = '<div class="fn">';
	$doc .= '<sup><a href="#fnt__'.$i.'" id="fn__'.$i.'" name="fn__'.$i.'" class="fn_bot">';
	$doc .= $i.')</a></sup> ';
	$doc .= $footnote;
	$doc .= '</div>' . NR;
	return $doc;
}



?>