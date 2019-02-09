<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Name: down cownt 2
 * Plugin URI: http://6log.ru/down-count2
 * Author: Tux
 *
 * Copyright 2009, Tux
 * Released under the GPL v.2
*/

# функция автоподключения плагина
function down_count2_autoload($args = array())
{
	mso_create_allow('down_count2_edit', t('Админ-доступ к настройкам счетчика переходов (Download count 2)', 'plugins')); 
	mso_hook_add( 'admin_init', 'down_count2_admin_init'); # хук на админку
	mso_hook_add( 'content', 'down_count2_content'); # хук на обработку текста
	mso_hook_add( 'init', 'down_count2_init'); # хук на обработку входящего url
}

# функция выполняется при деинстяляции плагина
function down_count2_uninstall($args = array())
{	
	mso_delete_option('plugin_down_count2', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function down_count2_admin_init($args = array()) 
{
	if ( !mso_check_allow('down_count2_edit') ) return $args;
			
	$this_plugin_url = 'plugin_down_count2'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Счетчик переходов 2', 'plugins'));
	mso_admin_url_hook ($this_plugin_url, 'down_count2_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function down_count2_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('down_count2_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Счетчик переходов', 'plugins') . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Счетчик переходов', 'plugins') . ' - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'down_count2/admin.php');
}

function down_count2_get_data()
{
	// вспомогательная опция, которая получает массив из файла
	// делаем её статик, чтобы не было многократного ображения к файлу
	// когда нужно получить данные из хука на контент
	
	global $MSO;
	static $data;
	
	if (!isset($data))
	{
		$options = mso_get_option('plugin_down_count2', 'plugins', array());
		if ( !isset($options['file']) ) $options['file'] = 'dc2.dat';

		$fn = $MSO->config['uploads_dir'] . $options['file'];
		
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (!file_exists( $fn )) // файла нет, нужно его создать
			write_file($fn, serialize(array())); // записываем в него пустой массив
		
		// массив данных
		// url => array ( count=>77 )
		$data = unserialize( read_file($fn) ); // поулчим из файла
	}

	return $data;
}


# функции плагина
function down_count2_init($args = array())
{
	global $MSO;
	
	# опции плагина
	$options = mso_get_option('plugin_down_count2', 'plugins', array());
	if ( !isset($options['prefix']) ) $options['prefix'] = 'dc';
	
	if (mso_segment(1) == $options['prefix'] and mso_segment(2)) 
	{
		if ( !isset($options['referer']) ) $options['referer'] = 1; // запретить скачку с чужих сайтов
		
		if ($options['referer'])
		{
			// если нет реферера, то рубим
			if (!isset($_SERVER['HTTP_REFERER'])) //
				die( sprintf('<b><font color="red">' . t('Данная ссылка доступна только со <a href="%s">страниц сайта</a>', 'plugins') . '</font></b>', getinfo('siteurl')) );
			
			// проверяем реферер - откуда пришел
			$p = parse_url($_SERVER['HTTP_REFERER']);
			if (isset($p['host'])) $p = $p['host'];
				else $p = '';
			if ( $p != $_SERVER['HTTP_HOST'] ) // чужой сайт
				die('<b><font color="red">' . t('Запрещен переход по этой ссылке с чужого сайта', 'plugins') . '</font></b>');
		}
		
		// это редирект на указанный в сегментах url
		
		$CI = &get_instance();
		
		$url = base64_decode(mso_segment(2)); // декодируем

		$url2 = $CI->input->xss_clean(urldecode($url));
		$url = urlencode($url2);
		
		if ($url == '') exit;
		
		// все урлы хранятся в файле 
		// в виде серилизованного массива
		if ( !isset($options['file']) ) $options['file'] = 'dc2.dat';
		
		$fn = $MSO->config['uploads_dir'] . $options['file']; // имя файла
		
//		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (!file_exists( $fn )) // файла нет, нужно его создать
			write_file($fn, serialize(array())); // записываем в него пустой массив
		
		// массив данных: url => array ( count=>77 )
		$data = unserialize( read_file($fn) ); // получим из файла
		
		if (isset($data[$url])) // такой url уже есть
			$data[$url]['count'] = $data[$url]['count'] + 1;
		else // нет еще
			$data[$url]['count'] = 1; // записываем один переход
		
		write_file($fn, serialize($data) ); // созраняем в файл

		header("Location: $url2"); // переход на файл
		exit;
	}
	return $args;
}


function down_count2_content_callback($matches)
{
	//'|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	
	// ститик, чтобы не получать каждый раз опции
	static $prefix, $format, $images;
	
	if (!isset($prefix) or !isset($format) or !isset($images)) 
	{
		$options = mso_get_option('plugin_down_count2', 'plugins', array());
		
		if ( !isset($options['prefix']) ) $options['prefix'] = 'dc';
		$prefix = $options['prefix'];
		
		if ( !isset($options['format']) ) $options['format'] = 
			'%IMG% %URL%<sup title="' . t('Количество переходов', 'plugins') . '">%COUNT%</sup>( %SIZE% )';
		$format = $options['format'];
		
		if ( !isset($options['images']) ) $options['images'] = 1;
		$images = $options['images'];
	}
	
	$data = down_count2_get_data(); // получаем массив из файла, в котором ведется подсчет колва переходов

	if (isset( $data[urlencode($matches[2])]['count'] )) $count = $data[urlencode($matches[2])]['count'];
		else $count = 0;
	
	$url  = base64_encode(urlencode($matches[2])) ; // кодируем урл в одну строку
	$url  = getinfo('siteurl') . $prefix . '/' . $url;

//////////////////
	$size = 0;
//	$root = $_SERVER['DOCUMENT_ROOT'];
	$file_ext = ''; 
	$img = '';
	
//	$example = 'http://site.ru/downloads/file.zip';
	$pattern = '@^(https?://)?([\da-z\.-]+)\.([a-z]{2,6})/([/_\da-z\.-]*)/?$@';
				//^(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\w\.]*)*\/?$

	$str = $matches[2];

	$file = check_file($str);

	if ($file == 'true')
	{
		$size = remote_filesize($str);
		$size = format_filesize($size);
		$file_ext = file_extension($str);
		$url_ = getinfo('plugins_url').'down_count2/images/'.$file_ext.'.gif';
		//echo $url;
		if ( (file_exists(getinfo('plugins_dir').'down_count2/images/'.$file_ext.'.gif')) && ($images == 1) )
		{
			$img = '<img src="'.$url_.'" />';
		}
	} 
	else $size = ''; //Error
	
	$link = '<a' . $matches[1] . 'href="' . $url . '"' . ' title="' . $matches[2] . '" '. $matches[3] . '>' . $matches[4] . '</a>';
	
	$out = str_replace(array('%SIZE%', '%URL%', '%IMG%', '%COUNT%'), 
							array($size, $link, $img, $count), $format );
	return $out;
}


# замена ссылок в тексте
function down_count2_content($text = '')
{
	// [dc]<a href="http://localhost/codeigniter/">ссылка</a>[/dc]
	$pattern = '|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	$text = preg_replace_callback($pattern, 'down_count2_content_callback', $text);

	return $text;
}

# Function: File Exists
function check_file($url)
{
	$Headers = @get_headers($url);
	if(strpos($Headers[0], '200')) 
	{
		$file = 'true';//echo "Файл существует";
	} 
	else 
	{
		$file = 'false';//echo "Файл не найден";
	}	
	return $file;
}
# Function: Get File Extension
function file_extension($filename) 
{
	$file_ext = explode('.', $filename);
	$file_ext = $file_ext[sizeof($file_ext)-1];
	$file_ext = strtolower($file_ext);
	return $file_ext;
}
# Function: Get Remote File Size
function remote_filesize($uri) 
{
	$header_array = @get_headers($uri, 1);
	$file_size = $header_array['Content-Length'];
	if(!empty($file_size)) 
	{
		return $file_size;
	} else {
		return 'Error';//('unknown');
	}
}
# Function: Format Bytes Into
function format_filesize($size) 
{
	if ($size > 1048576)
	{
		$size = round($size/1048576) . t(' Мб');
		return $size;
	}
	elseif ($size > 1024)
	{
		$size = round($size/1024) . t(' Кб');
		return $size;
	}
	elseif($size > 1) 
	{
		$size = round($size) . t(' байт');
		return $size;
	} 
	else 
	{
		return 'Error';
	}
}
?>