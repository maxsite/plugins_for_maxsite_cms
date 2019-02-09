<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Syntaxer» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-syntaxer
 */

# функция автоподключения плагина
function syntaxer_autoload($args = array())
{	
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow('syntaxer_options', 'Админ-доступ к опциям плагина «Syntaxer»');
		
	mso_hook_add('head', 'syntaxer_head'); # хук для подключения  необходимых скриптов и стилей на внешних страницах
	mso_hook_add('body_end', 'syntaxer_end'); # хук для вывода запускающего js-скрипта
		
	mso_hook_add('content', 'syntaxer_bbcontent', 100); # хук на обработку контента
	mso_hook_add('content', 'syntaxer_content'); # хук на обработку контента
		
	mso_hook_add('content_content', 'syntaxer_content_content'); # хук на обработку контента
}

# функция выполняется при активации (вкл) плагина
function syntaxer_activate($args = array())
{
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function syntaxer_deactivate($args = array())
{	
	return $args;
}

# функция выполняется при деинсталяции плагина
function syntaxer_uninstall($args = array())
{
	# удалим созданные опции
	mso_delete_option('plugin_syntaxer', 'plugins');
		
	# удалим созданные разрешения
	mso_remove_allow('syntaxer_options');
		
	return $args;
}

# формирование страницы опций в админке
function syntaxer_mso_options()
{
	if( !mso_check_allow('syntaxer_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	$options = mso_get_option('plugin_syntaxer', 'plugins', array());
		
	# выносим функции формирования массива опций в отдельный файл
	require(getinfo('plugins_dir').'syntaxer/backend-options.php');
}

# подключение подключение ядра SyntaxHighlighter на внешних страницах
function syntaxer_head($args = array())
{
	$plugin_url = getinfo('plugins_url').'syntaxer';
		
	#  Получаем настройки плагина
	$options = mso_get_option('plugin_syntaxer', 'plugins', array());
	if( count($options) == 0 )
	{
		return $args; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		
		# Подключение скриптов и стилей
		echo NR.TAB.'<!-- плагин syntaxer -->' . NR;
		#echo TAB.'<script src="'.$plugin_url.'/scripts/XRegExp.js" type="text/javascript"></script>'.NR;
		echo TAB.'<script src="'.$plugin_url.'/scripts/shCore.js" type="text/javascript"></script>'.NR;
		echo TAB.'<script src="'.$plugin_url.'/scripts/shAutoloader.js" type="text/javascript"></script>'.NR;
		echo TAB.'<link rel="stylesheet" href="'.$plugin_url.'/styles/shCore.css" type="text/css">'.NR;
		echo TAB.'<link rel="stylesheet" href="'.$plugin_url.'/styles/shThemeDefault.css" type="text/css">'.NR;
			
		if( $options['css'] <> '' )
		{
			# Опциональные стили	
			echo TAB.'<style>'.NR.$options['css'].TAB.'</style>'.NR;
		}
			
		$out = '';
		# подключение SyntaxHighlighter.defaults
		if( $options['defaults'] <> '' )
		{
			$defs = explode(NR, trim($options['defaults']));
				
			foreach($defs as $ln){
				$param = array_map('trim', explode('|', trim($ln)));
				$out .= TAB.TAB."SyntaxHighlighter.defaults['".$param[0]."'] = ".$param[1].";".NR;
			}
			$out .= NR;
		}
		# подключение SyntaxHighlighter.config
		if( $options['config'] <> '' )
		{
			$conf = explode(NR, trim($options['config']));
				
			foreach($conf as $ln){
				$param = array_map('trim', explode('|', trim($ln)));
				$out .= TAB.TAB."SyntaxHighlighter.config.".$param[0]." = ".$param[1].";".NR;
			}
			$out .= NR;
		}
		# подключение SyntaxHighlighter.config.strings
		if( $options['cfgstrings'] <> '' )
		{
			$conf = explode(NR, trim($options['cfgstrings']));
				
			foreach($conf as $ln){
				$param = array_map('trim', explode('|', trim($ln)));
				$out .= TAB.TAB."SyntaxHighlighter.config.strings.".$param[0]." = ".$param[1].";".NR;
			}
		}
		if( $out <> '' )
		{
			echo NR.
				TAB . '<script type="text/javascript">' . NR .
				$out.
				TAB . '</script>' . NR;
		}
			
		echo TAB.'<!-- /плагин syntaxer -->' . NR;
	}
	return $args;
}
# выврд запускающего скрипта в конце страницы
function syntaxer_end($args = array())
{
	$plugin_url = getinfo('plugins_url').'syntaxer';
		
	#  Получаем настройки плагина
	$options = mso_get_option('plugin_syntaxer', 'plugins', array());
	if( count($options) == 0 )
	{
		return $args; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		# подключение brushes
		if( $options['brushes'] <> '' )
		{
			$brushes = explode(NR, trim($options['brushes']));
			$out = TAB.'<script type="text/javascript">'.NR.
					TAB.TAB.'SyntaxHighlighter.autoloader('.NR;
			$last = count($brushes);
			foreach($brushes as $k => $ln){
				$params = array_map('trim', explode('|', trim($ln)));
				$types = array_map('trim', explode(',', trim($params[0])));
					
				#$out .= TAB.TAB.TAB."['".implode("', '", $types)."', '".$plugin_url."/scripts/".$params[1]."']";
				$out .= TAB.TAB.TAB."['".implode("', '", $types)."', '/application/maxsite/plugins/syntaxer/scripts/".$params[1]."']";
				if( $k + 1 != $last ) $out .= ",".NR;
			}
			$out .=  TAB.TAB.');'.NR.
				TAB.'</script>'.NR;
			echo $out;
		}
			
		echo NR.
			TAB . '<!-- плагин syntaxer -->' . NR .
			TAB . '<script type="text/javascript">' . NR .
			TAB . TAB . 'SyntaxHighlighter.all();' . NR .
			TAB . '</script>' . NR;
			TAB . '<!-- /плагин syntaxer -->' . NR;
	}
	return $args;
}

function syntaxer_content_content($text)
{
	$text = preg_replace('/\<p\>\<script type\=\"syntaxhighlighter\"(.*?)\<\!\[CDATA\[(\<\/p\>)?/mis', '<script type="syntaxhighlighter"\\1<![CDATA[', $text);
	$text = preg_replace('/\<p\>\]\]\>\<\/script\>/mis', ']]></script>'.NR, $text);
		
	$text = preg_replace_callback('/\<script type\=\"syntaxhighlighter\"(.*?)\<\!\[CDATA\[(.*?)\]\]\>\<\/script\>/mis', 'replace_2NR_callback', $text);
		
	return $text;
}

function replace_2NR_callback($matches)
{
	$text = preg_replace('/'.NR2.'/mis', NR, $matches[2]);
	$src = '<script type="syntaxhighlighter"'.$matches[1].'<![CDATA['.$text.']]></script>'.NR;
	return $src;
}

# обработка контента - поиск и замена [pre], [script] и др. тэгов и при необходимости,
function syntaxer_content($text)
{
	#  Получаем настройки плагина
	$options = mso_get_option('plugin_syntaxer', 'plugins', array());
	if( count($options) == 0 )
	{
		return $text; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		# Парсинг html-кода <pre lang="*">
		#<pre lang=html>
		$text = preg_replace_callback('/\<pre(\=([a-zA-Z]+)[\s]?|\s(lang=([a-zA-Z]+)))([\s]?(.*?)|(.*?)|)\>/mis', 'parse_pre_lang', $text);
		$text = preg_replace_callback('/\<pre\sclass=([\"\']+?)(.*?)(\\1)(.*?)\>/mis', 'parse_pre_class', $text); # упорядочивание свойства class
			
		# обработка бб-кода [src]
		$text = preg_replace_callback('/\['.$options['bb_script'].'(\=([a-zA-Z]+)[\s]?|\s(lang=([a-zA-Z]+)[\s\]]?))\](.*?)\[\/'.$options['bb_script'].'\]/si', 'bb_script_callback', $text);
	}
	return $text;
}

function bb_script_callback($matches)
{
	$src = '<script type="syntaxhighlighter" class="brush:'.$matches[2].'"><![CDATA['.NR.$matches[5].NR.']]></script>'.NR;
	return $src;
}

function parse_pre_lang($match)
{
	if( isset($match[4]) and $match[4]<>'' )
	{
		$lang = str_replace(';', '', $match[4]);#2
	}
	elseif( isset($match[2]) and $match[2]<>'' )
	{
		$lang = str_replace(';', '', $match[2]);#2
	}

	preg_match_all("/class\=([\"\']?)(.*?)\\1/mis", $match[5], $found); #3
	
	$misc = trim(preg_replace('/[\s]?class\=([\"\']?)(.*?)\\1/mis', '', $match[5]));
	
	$cn = ''; # для хранения содержимого class-name
	$cv = ''; # для хранения остальных значений свойства class

	if( isset($found[2][0]) )
	{
		foreach( $found[2] as $class )
		{
			$res = parse_class_name($class);
			$cn .= $res['cn'];
			$cv .= $res['cv'];
		}
	}
		
	if( isset($cn) and $cn <> '' )
	{
		$cn = ' class-name:\''.trim($cn).'\';';
	}
	
	if( isset($cv) and  $cv <> '' )
	{
		$cv = ' '.trim($cv).'';
	}
	
	if( $misc <> '' )
	{
		$misc = ' '.$misc;
	}
		
	if( !preg_match("/brush:/", $cv) and isset($lang) ) # если нет определения языка
	{
		$cv = 'brush:'.$lang.';'.$cv;
	}
	else
	{
		$cv = trim($cv);
	}
	
	$pre = 'class="'.$cv.$cn.'"'.$misc;
	#pr($match);
	#pr($pre);
	#exit;
		
	return '<pre '.$pre.'>';
}

function parse_pre_class($match)
{
	
	$res = parse_class_name($match[2]);
	$cn = $res['cn'];
	$cv = $res['cv'];
	$misc = $match[4];
		
	if( isset($cn) and $cn <> '' )
	{
		$cn = ' class-name:\''.trim($cn).'\';';
	}
	
	if( isset($cv) and  $cv <> '' )
	{
		$cv = trim($cv).'';
	}
	
	if( $misc <> '' )
	{
		$misc = ' '.trim($misc);
	}
		
	$pre = 'class="'.$cv.$cn.'"'.$misc;
	#pr($res);
	#pr($pre);	
		
	return '<pre '.$pre.'>';
}

function parse_class_name($text)
{
	#pr($text);
	$res = array('cn' => '', 'cv' => '');
		
	# есть служебные слова от syntaxhighliter ?
	$words = 'brush|class-name|pad-line-numbers|highlight|title|smart-tabs|tab-size|gutter|toolbar|quick-code|collapse|auto-links|light|unindent|html-script';
	if( preg_match("/(".$words.")/", $text) )
	{
		preg_match_all("/class\-name\:\s?([\"\']?)([^\'\"]+?)(\\1?)(\;\s|\;|$)/mis", $text, $names);
			
		if( isset($names[2][0]) )
		{
			foreach( $names[2] as $name )
			{
				$res['cn'] .= $name.' ';
			}
		}
			
		$res['cv'] .= trim(preg_replace('/[\s]?class\-name\:\s?([\"\']?)([^\'\"]+?)(\\1?)(\;\s|\;|$)/mis', '', $text)).' ';
	}
	else
	{
		$res['cn'] .= $text.' ';
	}
		#pr($res);
	return $res;
}

function syntaxer_bbcontent($text)
{
	#  Получаем настройки плагина
	$options = mso_get_option('plugin_syntaxer', 'plugins', array());
	if( count($options) == 0 )
	{
		return $text; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		$text = preg_replace_callback('/\[pre(.*?)\[\/pre\]/mis', 'skob', $text);
	}
	return $text;
}
/*************************************************************************************************************************************/
function skob($match)
{
	$m = $match[1];
	$m = preg_replace('/\[([\d,\s]+?)\]/mis', '($1)', $m);
	return '[pre'.$m.'[/pre]';
}

?>