<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Highslide» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-highslide
 */

# функция автоподключения плагина
function highslide_autoload($args = array())
{	
	# Определяем опции для управления правами доступа к настройке плагина
	mso_create_allow('highslide_options', 'Админ-доступ к опциям плагина «Highslide»');
		
	mso_hook_add( 'head', 'highslide_head'); # хук для подключения  необходимых скриптов и стилей на внешних страницах
	mso_hook_add( 'admin_head', 'highslide_head'); # хук для подключения  необходимых скриптов и стилей на страницах админки
		
	mso_hook_add( 'content_out', 'highslide_content'); # хук на вывод контента после обработки всех тэгов
}

# функция выполняется при активации (вкл) плагина
function highslide_activate($args = array())
{
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function highslide_deactivate($args = array())
{	
	return $args;
}

# функция выполняется при деинсталяции плагина
function highslide_uninstall($args = array())
{
	# удалим созданные опции
	mso_delete_option('plugin_highslide', 'plugins');
		
	# удалим созданные разрешения
	mso_remove_allow('highslide_options');
		
	return $args;
}

# формирование страницы опций в админке
function highslide_mso_options()
{
	if( !mso_check_allow('highslide_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	$options = mso_get_option('plugin_highslide', 'plugins', array());
	if( !isset($options['slug']) ) $options['slug'] = 'highslide';
		
	# выносим функции формирования массива опций в отдельный файл
	require(getinfo('plugins_dir').'highslide/backend-options.php');
}

# подключение своих стилей и скриптов на внешних страницах
function highslide_head($args = array())
{
	#  Получаем настройки плагина
	$options = mso_get_option('plugin_highslide', 'plugins', array());
	if( count($options) == 0 )
	{
		return $args; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		# задаём значения по-умолчанию, если не заданы обязательные. Подгототавливаем значения опций к удобному использованию
		if( !isset($options['js_file']) ) $options['js_file'] = '0';
		$js_file = array();
		$js_file[0] = 'highslide.packed.js';
		$js_file[1] = 'highslide-with-html.packed.js';
		$js_file[2] = 'highslide-with-gallery.packed.js';
		$js_file[3] = 'highslide-full.packed.js';
			
		if( !isset($options['adminka']) ) $options['adminka'] = false;
	}
		
	if(mso_segment(1)=='admin' and !$options['adminka'])
	{
		return $args;
	}
		
	echo NR.TAB.TAB.'<!-- плагин highslide -->'.NR; # начало подключения в секции HEAD
		
	# Стили
	echo TAB.TAB.'<link rel="stylesheet" type="text/css" href="'.getinfo('plugins_url').'highslide/inc/highslide.css" />'.NR; # Стандартные стили библиотеки
	echo TAB.TAB.'<!--[if lt IE 7]>'.NR;
	echo TAB.TAB.'<link rel="stylesheet" type="text/css" href="'.getinfo('plugins_url').'highslide/inc/highslide-ie6.css" />'.NR;
	echo TAB.TAB.'<![endif]-->'.NR;
	
	if( isset($options['css']) and $options['css'] <> '' )
	{
		echo TAB.TAB.'<style>'.NR;
		$lines = explode(NR, $options['css']);
		foreach( $lines as $line )
		{
			echo TAB.TAB.$line.NR;
		}
		echo TAB.TAB.'</style>'.NR;
	}
		
	# Javascript
	echo TAB.TAB.'<script type="text/javascript" src="'.getinfo('plugins_url').'highslide/js/'.$js_file[$options['js_file']].'"></script>'.NR;
		
	echo TAB.TAB.'<script type="text/javascript">'.NR;
		
	# VARIABLES
	$vars = array();
	if( (isset($options['variables']) and $options['variables'] <> '') )
	{
		$params = explode(NR, trim($options['variables']));
		foreach($params as $k => $par)
		{
			$v = array_map('trim', explode('|', trim($par)));
			echo TAB.TAB.TAB."hs.".$v[0]." = ".$v[1].";".NR;
			$vars[strtolower($v[0])] = $v[1];
		}
	}
	if( !isset($variables['graphicsdir']) )
	{
		echo TAB.TAB.TAB."hs.graphicsDir = '/".str_replace(getinfo('siteurl'), '', getinfo('plugins_url'))."highslide/inc/graphics/';".NR;
	}
		
	# Есть произвольный js-скрипт настройки
	if( (isset($options['custom_js']) and $options['custom_js'] <> '') )
	{
		if( (isset($options['variables']) and $options['variables'] <> '') )
		{
			echo NR;
		}
		echo TAB.TAB.TAB."/* custom_js */".NR;
		$lines = explode(NR, $options['custom_js']);
		foreach( $lines as $line )
		{
			echo TAB.TAB.TAB.$line.NR;
		}
	}
		
	# Языковые настройки
	$lang = array();
	if( isset($options['lang_file']) and $options['lang_file'] <> 'default' )
	{
		$lang_file = file( getinfo('plugins_dir').'highslide/lang/'.$options['lang_file'] ); unset($lang_file[0]); # читаем файл с переводом строк сообщений и удаляем первую строчку с названием языка
		if( count($lang_file) > 0 )
		{
			foreach( $lang_file as $line )
			{
				$param = array_map('trim',explode(':', trim($line)));
				$key = $param[0]; unset($param[0]);
				$value = implode(':', $param);
				$lang[$key] = $value;
			}
		}
	}
	if( isset($options['custom_lang']) and $options['custom_lang'] <> '' )
	{
		$params = explode(NR, trim($options['custom_lang']));
		foreach( $params as $line )
		{
			$param = explode('|', trim($line));
			$lang[$param[0]] = $param[1];
		}
	}
	if( count($lang) > 0 )
	{
		foreach( $lang as $key => $value )
		{
			$value = preg_replace('/\,$/', ';', $value);
			echo TAB.TAB.TAB.'hs.lang.'.$key.' = '.$value.NR;
		}
	}
		
	echo TAB.TAB.'</script>'.NR;
		
	echo TAB.TAB.'<!-- / плагин highslide -->'.NR2; # конец подключения в секции HEAD
		
	return $args;
}

# обработка контента
function highslide_content($text = '')
{
	#  Получаем настройки плагина
	$options = mso_get_option('plugin_highslide', 'plugins', array());
	if( count($options) == 0 )
	{
		return $text; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		# если нет скрипта для обработки галереи, но на странице галерея всё же есть, то надо добавить минимальный скрипт для hs.addSlideshow
		$src  =	'';
		if( preg_match("~\[gallery(.*?)\]~si", $text) and ( isset($options['custom_js']) and !preg_match("~addSlideshow~si", $options['custom_js']) ) and (isset($options['gal_def']) and $options['gal_def'] <> '') )
		{
			$src .=	NR.'<!-- плагин highslide -->'.NR; # начало вывода настроек hs.addSlideshow
			$src .=	'<script type="text/javascript">'.NR;
			$src .=	TAB.'if (hs.addSlideshow) hs.addSlideshow({'.NR;
				
			$lines = array_map('trim', explode(NR, $options['gal_def']));
			foreach( $lines as $line )
			{
				$src .=	TAB.TAB.$line.NR;
			}
				
			$src .=	TAB.'});'.NR;
			$src .=	'</script>'.NR;
			$src .=	'<!-- / плагин highslide -->'.NR2; # конец вывода настроек hs.addSlideshow
		}
			#pr($text);	exit;
		$preg = array(
			# удаление раcставленных параграфов
			'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
			'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
			'~\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
			'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
			'~<p>\[\/gallery\]~si' => '[/gallery]',
			'~<p>\[gallery(.*?)\](\s)*~si' => '[gallery$1]',
			'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',
			'~<p>\[galname(.*?)\[\/galname\]</p>~si' => '[galname$1[/galname]',
			'~<p>\[hidegal\]</p>~si' => '[hidegal]',
			'~<p>\[\/hidegal\]</p>~si' => '[/hidegal]',
			'~<p>\[hidegal(.*?)\[\/hidegal\]</p>~si' => '[hidegal$1[/hidegal]',

			# обрабатываем одиночные изображения
			'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="highslide" onclick="return hs.expand(this)"><img src="$1" alt=""></a>',
			'~\[image=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="highslide" title="$2" onclick="return hs.expand(this)"><img src="$1" alt="$2"></a>',
			'~\[image=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="highslide" onclick="return hs.expand(this)"><img src="$1" alt=""></a>',
			# [image(left)=/uploads/mini/picture.jpg Картинка]/uploads/picture.jpg[/image]
			'~\[image\((.[^\s]*?)\)=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$4" class="highslide" title="$3" onclick="return hs.expand(this)"><img src="$2" alt="$3" class="$1"></a>',
			# [image(left)=/uploads/mini/picture.jpg]/uploads/picture.jpg[/image]
			'~\[image\((.[^ ]*?)\)=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="highslide" onclick="return hs.expand(this)"><img src="$2" alt="" class="$1"></a>',
			# [image(right)]/uploads/picture.jpg[/image]
			'~\[image\((.[^ ]*?)\)\](.*?)\[\/image\]~si' => '<a href="$2" class="highslide" onclick="return hs.expand(this)"><img src="$2" alt="" class="$1"></a>',

			# формируем контейнер галереи
			'~\[gallery=(.*?)\](.*?)\[\/gallery\]~si' => '<div group="$1">$2</div>[group=$1]',
			'~\[gallery\](.*?)\[\/gallery\]~si' => '<div group="noname">$1</div>[group=noname]',
			
			# обрабатываем каждое изображение в галерее
			'~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<a href="$3" title="$2" class="highslide" onclick="return hs.expand(this)"><img src="$1" alt="$2"></a><div class="highslide-caption">$2</div>',
			'~\[gal=(.*?)\](.*?)\[\/gal\]~si' => '<a href="$2" class="highslide" onclick="return hs.expand(this)"><img src="$1" alt=""></a>',
				
			# заголовок изображения в галерее
			'~\[galname\](.*?)\[\/galname\]~si' => '<div class="highslide-caption">$1</div>',
				
			# скрытые изображения в галерее
			'~\[hidegal\](.*?)\[\/hidegal\]~si' => '<div class="hidden-container">$1</div>',
		);
			
		$text  = preg_replace(array_keys($preg), array_values($preg), $text);
		$text  = preg_replace_callback('~\<div group\=\"(.*?)\"\>(.*?)\<\/div\>\[group\=\\1\]~si', 'highslide_content_group_callback', $text);
		$text .= $src;
			
		return $text;
	}
}

function highslide_content_group_callback($matches)
{
	$group = $matches[1];
	$inner = $matches[2];
	$options = mso_get_option('plugin_highslide', 'plugins', array());
	
	$inner = preg_replace("~hs\.expand\(this\)~si", "hs.expand(this,{slideshowGroup:'".$group."'})", $inner);
		
	if( isset($options['gal_img_hide']) and $options['gal_img_hide'] == 1 )
	{
		return '<div class="highslide-gallery-'.$group.'">'.$inner.'</div>';
	}
	else
	{
		return '<div class="highslide-gallery">'.$inner.'</div>';
	}
}
?>