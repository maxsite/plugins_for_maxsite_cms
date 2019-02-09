<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Down Counter» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков
 * Plugin URL: http://maxhub.ru/page/plugin-down-counter
 */

# функция автоподключения плагина
function down_counter_autoload( $args = array() )
{
	# Создаём опции настройки прав доступа
	mso_create_allow(basename(dirname(__FILE__)).'_options', 'Админ-доступ к опциям плагина «Down Counter»');
	mso_create_allow(basename(dirname(__FILE__)).'_edit', 'Админ-доступ к плагину «Down Counter»');
		
	mso_hook_add('init', basename(dirname(__FILE__)).'_init'); # хук на обработку входящего url
	mso_hook_add('head', basename(dirname(__FILE__)).'_head'); # хук для подключения стилей на внешних страницах
	mso_hook_add('content', basename(dirname(__FILE__)).'_content'); # хук на обработку текста
		
	mso_hook_add('admin_announce', basename(dirname(__FILE__)).'_admin_announce'); # хук на вывод своей вкладки на странице «Информация»
	mso_hook_add('admin_init', basename(dirname(__FILE__)).'_admin_init'); # хук на админку
	mso_hook_add('admin_head', basename(dirname(__FILE__)).'_admin_head'); # хук для подключения стилей на внутренних страницах
		
	# получаем опции плагина
	$options = down_counter_get_options();
		
	# Подключаем кнопки в редакторах
	if( isset($options['editor_buttons']) && $options['editor_buttons'] == 1 )
	{
		# editor_jw
		mso_hook_add('editor_controls_extra_css', basename(dirname(__FILE__)).'_editor_controls_extra_css');
		mso_hook_add('editor_controls_extra', basename(dirname(__FILE__)).'_editor_controls_extra');
			
		# markItUp
		mso_hook_add('editor_markitup_bbcode', basename(dirname(__FILE__)).'_editor_markitup_bbcode');
	}
		
	# включаем обработку бб-кода в комментариях
	if( isset($options['comments']) && ( $options['comments'] == 1 ) )
	{
		mso_hook_add('comments_content_out', basename(dirname(__FILE__)).'_content'); # хук на обработку комментариев
	}
}

# функция выполняется при деинстяляции плагина
function down_counter_uninstall( $args = array() )
{	
	# удалим созданные разрешения
	mso_remove_allow(basename(dirname(__FILE__)).'_options');
	mso_remove_allow(basename(dirname(__FILE__)).'_edit');
		
	mso_delete_option('plugin_'.basename(dirname(__FILE__)), 'plugins' ); # удалим созданные опции
		
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function down_counter_mso_options() 
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	# получаем опции плагина
	$options = down_counter_get_options();
		
	# подключаем файл с определением опций
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/settings.php' );
		
	# подключаем файл вывода меню
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-menu.php' ); 
		
	# ключ, тип, ключи массива
	mso_admin_plugin_options2('plugin_'.basename(dirname(__FILE__)), 'plugins', 
		$settings,
		'Настройки плагина «Down Counter»', # титул
		'<p class="info">С помощью этого плагина вы можете подсчитывать количество скачиваний или переходов по ссылке. Для использования плагина обрамите нужную ссылку в код [dc]ваша ссылка[/dc]. Обрабатываются ссылки заданные в html формате (тэг <<b>a</b>>) и в формате bb-code (тэг [<b>url</b>]).</p>' # инфо
	);
		
	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}

# функция выполняется при указаном хуке admin_init
function down_counter_admin_init($args = array()) 
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_edit') ) return $args;
			
	# получаем опции плагина
	$options = down_counter_get_options();
		
	if( !isset($options['show_admin_sidebar_link']) ) $options['show_admin_sidebar_link'] = 0;
		
	$this_plugin_url = basename(dirname(__FILE__)); // url и hook
		
	if( $options['show_admin_sidebar_link'] == 1 )
	{
		mso_admin_menu_add('plugins', $this_plugin_url, t('Счетчик переходов'));
	}
		
	mso_admin_url_hook ($this_plugin_url, basename(dirname(__FILE__)).'_admin_page');
		
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function down_counter_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('down_counter_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Счетчик переходов') . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Счетчик переходов') . ' - " . $args; ' );
		
	# получаем опции плагина
	$options = down_counter_get_options();
		
	require(getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-admin.php');
		
	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}

# функция подключения дополнительных стилей и скриптов для работы плагина в админке
function down_counter_admin_head( $args = array() )
{
	# получаем опции плагина
	$options = down_counter_get_options();
		
	if( mso_segment(1) == 'admin' )
	{
		# Стили	
		echo '
			<style>
				div.sidebar ul.admin-menu ul.admin-submenu li.admin-menu-down_counter a:before {
					content: "\f012";
				}
			</style>
		';
	}
		
	if( mso_segment(1) == 'admin' && ( mso_segment(2) == '' || mso_segment(2) == 'home' || mso_segment(2) == 'down_counter' || mso_segment(3) == 'down_counter' ) )
	{
		# общие стили для админ-панели
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/backend-all.css" type="text/css" media="screen">'.NR;
	}
		
	# Подключение необходимого на странице опций плагина
	# admin/plugin_options/down_counter
	if( mso_segment(1) == 'admin' && mso_segment(2) == 'plugin_options' && mso_segment(3) == basename(dirname(__FILE__)) )
	{
		# стили для страницы опций
		if( $fn = mso_fe('backend-options.css', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) echo mso_load_style( getinfo('plugins_url').basename(dirname(__FILE__)).'/backend-options.css' );
	}
		
	# Подключаем стили для кнопок в редакторы
	if( mso_segment(1) == 'admin' && ( mso_segment(2) == 'page_edit' || mso_segment(2) == 'page_new' ) && isset($options['editor_buttons']) && $options['editor_buttons'] == 1 )
	{
		echo mso_load_style( getinfo('plugins_url').basename(dirname(__FILE__)).'/editor-options.css' );
	}
		
	return $args;
}


# подключение своих стилей на внешних страницах
function down_counter_head( $args = array() )
{
	# получаем опции плагина
	$options = down_counter_get_options();
		
	# стили пользователя
	if( $fn = mso_fe('custom.css', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) echo mso_load_style( getinfo('plugins_url').basename(dirname(__FILE__)).'/custom.css' );
		
	if( $options['css'] <> '' )
	{
		# Опциональные стили	
		echo '<style>'.NR.$options['css'].NR.'</style>'.NR;
	}

	# js-код обработчика
	if( $options['jsclicks'] )
	{
		# jQuery код для обработки ссылок
		echo '<script src="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/down_counter.js"></script>'.NR; # подключаем js
	}
		
	return $args;
}

# функция выполняется при указаном хуке admin_announce
function down_counter_admin_announce( $args = array() ) 
{
        $args[] = array(
		'Статистика переходов',
		down_counter_stat_tbl_generate().
		'<div class="admin-h-menu">'.NR.
		'<a class="options" href="'.getinfo('site_admin_url').'plugin_options/'.basename(dirname(__FILE__)).'" class="select">Настройки</a>'.
		'<a class="edit" href="'.getinfo('site_admin_url').basename(dirname(__FILE__)).'/edit">Правка данных</a>'.
		'</div>'.NR
	);
		
	return $args;
}

# получение массива данных их файла
function down_counter_get_data( $lines = false )
{
	# вспомогательная функция, которая получает массив из файла
	# делаем её статик, чтобы не было многократного обращения к файлу
	# когда нужно получить данные из хука на контент
	
	static $data;
	
	if( !isset($data) )
	{	
		$metka = 'DOWN COUNT'.NR; # метка в файле - первая строчка
		
		# получаем опции плагина
		$options = down_counter_get_options();
			
		$fn = getinfo('uploads_dir').$options['file'];
		
		if( $lines === false )
		{
			$CI = & get_instance();
			$CI->load->helper('file'); # хелпер для работы с файлами
		
			if( !file_exists($fn) ) # файла нет, нужно его создать
			{
				write_file($fn, $metka); # записываем метку
				$data = array($metka); # сразу формируем результирующий массив
			}
			else
			{
				$data = file($fn); # получим файл
			}
		}
		else
		{
			$data = $lines;
		}
	
		if( ( isset($data[0]) && $data[0] == $metka) || $lines ) # первая строчка - метка
		{
			# если есть метка, то данные в массиве хранятся в виде обычных строк адрес || count
			# нужно переделать в структуру массива [адрес]['count'] = 47746
			
			$data_new = array(); # сразу готовим массив данных
			
			foreach( $data as $link )
			{
				$a = array_map('trim', explode('||', $link));
					
				if( isset($a[0]) && $a[0] != 'DOWN COUNT' )
				{
					$url = down_counter_get_url(mso_xss_clean($a[0]));
					
					if( $url )
					{
						# рабтаем с дублями и формируем массив ссылок
						if( !isset($data_new[$url]) )
						{
							$data_new[$url]['count'] = isset($a[1]) ? $a[1] : 0;
							$data_new[$url]['desc'] = isset($a[2]) ? $a[2] : ''; # описание
						}
						else
						{	# если такой урл уже есть, то добавляем информацию об относительной ссылке
							$data_new[$url]['count'] = $data_new[$url]['count'] + (isset($a[1]) ? $a[1] : 0);
							$data_new[$url]['desc'] = $data_new[$url]['desc'] . ' ' . (isset($a[2]) ? $a[2] : '');
						}
					}
				}
			}
				
			$data = $data_new;
		}
		else
		{
			# это старый формат файла - серилизация
			$data = unserialize(read_file($fn)); # получим из файла
				
			# нужно обновить формат файла
				
			$data_new = array(); # сразу готовим обновленный массив
			
			foreach( $data as $url => $aaa )
			{
				$url = down_counter_get_url(mso_xss_clean($url));
					
				if( $url )
				{
					if( !isset($data_new[$url]) )
					{
						$data_new[$url]['count'] = isset($aaa['count']) ? $aaa['count'] : 0;
						$data_new[$url]['desc'] = ''; # описание
					}
					else
					{	# если такой урл уже есть, то добавляем информацию об относительной ссылке
						$data_new[$url]['count'] = $data_new[$url]['count'] + (isset($aaa['count']) ? $aaa['count'] : 0);
						$data_new[$url]['desc'] = $data_new[$url]['desc'];
					}
				}
			}
				
			$data = $data_new;
				
			down_counter_save_data($data); # записываем новый файл
		}
	}
		
	return $data;
}

# функции плагина
function down_counter_init( $args = array() )
{
	# получаем опции плагина
	$options = down_counter_get_options();
		
	if( mso_segment(1) == $options['prefix'] && mso_segment(2) )
	{
		if( $options['referer'] )
		{
			# если нет реферера, то рубим
			if( !isset($_SERVER['HTTP_REFERER']) )
			{
				die( sprintf('<b><font color="red">' . t('Данная ссылка доступна только со <a href="%s">страниц сайта</a>') . '</font></b>', getinfo('siteurl')) );
			}
			
			# проверяем реферер - откуда пришел
			$p = parse_url($_SERVER['HTTP_REFERER']);
			$p = isset($p['host']) ? $p['host'] : '';
			if( $p != $_SERVER['HTTP_HOST'] ) # чужой сайт
			{
				die('<b><font color="red">' . t('Запрещен переход по этой ссылке с чужого сайта') . '</font></b>');
			}
		}
		
		# это редирект на указанный в сегментах url
		$url = base64_decode(mso_segment(2)); # декодируем
		
		# проверяем входящий url - в нем может быть закодирована какая-то гадость
		$url_check = mso_xss_clean($url);
		if( $url_check != $url ) die('<b><font color="red">Achtung! XSS attack!</font></b>');
		
		$url = down_counter_get_url($url_check);
		
		# получим данные
		$data = down_counter_get_data();
		
		# засчитываем переход по ссылке, если такой url уже есть или записываем первый переход
		$data[$url]['count'] = isset($data[$url]) ? $data[$url]['count'] + 1 : 1;
		
		# сохраняем в файл 
		down_counter_save_data($data);

		mso_redirect($url, true);
			
		exit;
	}
		
	return $args;
}

# обрабатываем '|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
function down_counter_content_callback( $matches )
{
	# получаем опции плагина
	$options = down_counter_get_options();
		
	$data = down_counter_get_data(); # получаем массив из файла, в котором ведется подсчет кол-ва переходов
		
	$key = down_counter_get_url($matches[3]);
		
	$count = isset( $data[$key]['count'] ) ? $data[$key]['count'] : 0;
		
	$url  = base64_encode($key); # кодируем урл в одну строку
	$url  = getinfo('siteurl').$options['prefix'].'/'.$url;
		
	$format_out = ( $options['show_counter'] ) ? str_replace('%COUNT%', $count, $options['format']) : '';
		
	$matches[2] = str_replace('%COUNT%', $count, $matches[2]);
	$matches[4] = str_replace('%COUNT%', $count, $matches[4]);
		
	$title = ( $options['real_title'] ) ? ' title="' . $key . '" ' : ' ';
	$nofollow = ( $options['nofollow'] && strpos($matches[2].' '.$matches[4], 'nofollow') === false && strpos($matches[2].' '.$matches[4], 'follow') === false ) ? ' rel="nofollow"' : '';
	$jsclicks = $options['jsclicks'] ? ' down-counter="'.$matches[3].'"' : '';

	$matches[1] = array_map('trim', explode(' ', $matches[1]));
	if( $matches[1] != '' && count($matches[1]) > 0 )
	{
		# показываем счётчик принудительно, если есть атрибут
		if( ( in_array('show', $matches[1]) || in_array('+', $matches[1]) ) && $format_out == '' )
		{
			$format_out = str_replace('%COUNT%', $count, $options['format']);
		}

		# скрываем счётчик принудительно, если есть атрибут
		if( ( in_array('hide', $matches[1]) || in_array('-', $matches[1]) ) && $format_out != '' )
		{
			$format_out = '';
		}
	}
	$out = '<a'.$matches[2].'href="'.$url.'"'.$nofollow.$jsclicks.$title.$matches[4].'>'.$matches[5].'</a>'.$format_out;
		
	return $out;
}

function down_counter_content_callback_url( $matches )
{
	# используем down_counter_content_callback, только изменим под него $matches
		
	$m[2] = ' ';
	$m[3] = $matches[2];
	$m[4] = '';
	$m[5] = $matches[3];
		
	return down_counter_content_callback($m);
}

# замена ссылок в тексте
function down_counter_content( $text = '' )
{
	# [dc]<a href="http://localhost/codeigniter/">ссылка</a>[/dc]
	$pattern = '|\[dc(.*?)\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	$text = preg_replace_callback($pattern, 'down_counter_content_callback', $text);
		
	# [dc][url=урл]сайт[/url][/dc]
	$pattern = '|\[dc(.*?)\]\[url=(.*?)\](.*?)\[/url\]\[/dc\]|ui';
	$text = preg_replace_callback($pattern, 'down_counter_content_callback_url', $text);
		
	return $text;
}

# формирование таблицы со статистикой
function down_counter_stat_tbl_generate()
{
	# получаем опции плагина
	$options = down_counter_get_options();
		
	$data = down_counter_get_data();
	if( $data )
	{
		$out = '';
			
		$CI = & get_instance();
		$CI->load->library('table');
		$tmpl = array (
			'table_open'	  => '<table class="page tablesorter">',
			'row_alt_start'	  => '<tr class="alt">',
			'cell_alt_start'  => '<td class="alt">',
		);
			
		for( $i = 0; $i <= (int) $options['split_data_on_tables']; $i++ )
		{
			$has_row = false; # признак, что есть строки в таблице
				
			$CI->table->set_template($tmpl);
			$CI->table->set_heading('URL/Описание', t('переходов'));
				
			foreach( $data as $url => $aaa )
			{
				$url = down_counter_get_url(mso_xss_clean($url));
				
				if( $i == 0 && $options['split_data_on_tables'] && strpos($url, getinfo('siteurl')) === false ) continue;
				if( $i == 1 && $options['split_data_on_tables'] && strpos($url, getinfo('siteurl')) !== false ) continue;

				if( strpos($url, getinfo('uploads_url').'_pages/') === 0 )
				{
					$edit_url = explode('/', str_replace(getinfo('uploads_url').'_pages/', '' , $url));
					$edit = ' <a href="/admin/files/_pages/'.$edit_url[0].'" class="file_edit_link">&#xf044;</a>';
				}
				elseif( strpos($url, getinfo('uploads_url')) === 0 )
				{
					$edit_url = explode('/', str_replace(getinfo('uploads_url'), '' , $url));
					$edit = ' <a href="/admin/files'.(isset($edit_url[1]) ? '/'.$edit_url[0] : '').'" class="file_edit_link">&#xf044;</a>';
				}
				else
				{
					$edit = '';
				}
					
				$CI->table->add_row(
					'<a href="'.$url.'" target=_blank>'.$url.'</a>'.$edit.( $data[$url]['desc'] != '' ? '<p class="admin_page_qhint">'.$data[$url]['desc'].'</p>': ''),
					$data[$url]['count']
				);
				$has_row = true;
			}
				
			if( $has_row )
			{
				if( $i == 0 && $options['split_data_on_tables'] )
				{
					$out .= '<h2 class="down_counter">Локальные ссылки</h2>'.NR;
				}

				if( $i == 1 && $options['split_data_on_tables'] )
				{
					$out .= '<h2 class="down_counter">Внешние ссылки</h2>'.NR;
				}
					
				$out .= $CI->table->generate();
			}
		}
			
		return $out; 
	}
	else
	{
		return '';
	}
}

function down_counter_get_url( $url = '' )
{
	if( $url == '' ) return $url;
		
	# получаем опции плагина
	$options = down_counter_get_options();
		
	$options['ignore_begin'] = array_map('trim', explode('|', $options['ignore_begin']));
		
	if( $options['rewrite_related_urls'] )
	{
		if( !preg_match('/^(http\:\/\/|https\:\/\/|mailto\:|ftp\:\/\/|\/\/)/', $url) )
		{
			if( count($options['ignore_begin']) > 0 )
			{
				foreach( $options['ignore_begin'] as $ign )
				{
					if( $ign && strpos($url, $ign) !== false )
					{
						return $url; # прерываем проверку
					}
				}
			}
				
			return getinfo('siteurl').( substr($url, 0, 1) != '/' ? $url : substr($url, 1) );
		}
		else
		{
			return $url;
		}
	}
	else
	{
		return $url;
	}
}

# запись массива в файл
function down_counter_save_data( $data )
{
	# получаем опции плагина
	$options = down_counter_get_options();
		
	$fn = getinfo('uploads_dir') . $options['file'];
		
	$CI = & get_instance();
	$CI->load->helper('file'); # хелпер для работы с файлами
		
	# $data - массив
	# нужно его переделать в набор строк адрес || count || page_id
		
	$out = 'DOWN COUNT'.NR;
		
	foreach( $data as $url => $aaa )
	{
		$url = down_counter_get_url(mso_xss_clean($url));

		if( $url )
		{
			$out .= $url.' || '.$aaa['count'].( isset($aaa['desc']) ? ' || '.$aaa['desc'] : '' ).NR;
		}
	}
	
	write_file($fn, $out); // записываем новый файл
}

# Подключаем css-стиль своей кнопки в редакторе editor_nic
function down_counter_editor_controls_extra_css( $args = array() )
{
	echo '
	<style>
		div.wysiwyg ul.panel li a.e_dcounter_a {background: url(\''.getinfo('plugins_url').basename(dirname(__FILE__)).'/dcounter.png\') no-repeat scroll 0 0 transparent;}
	</style>'.NR;
		
	return $args;
}

# Интеграция в editor_nic
function down_counter_editor_controls_extra( $args = array() )
{
	# запятая в начале обязательна!
	echo <<<EOF
	, 

	e_dcounter_a : 
	{
		visible : true,
		title : 'Счётчик переходов',
		className : 'extra e_dcounter_a',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '[dc]' + selection + '[/dc]');
		}
	},
	separator3 : { separator : true }
	
EOF;
	# в конце запятой не должно быть!
		
	return $args;
}

# Интеграция в editor_markitup
function down_counter_editor_markitup_bbcode( $args = array() )
{
	echo <<<EOF
		{separator:'---------------' },	
		
		{name:'Счётчик переходов', openWith:'[dc][/dc]', className:"dcounter", dropMenu: [
			{name:'[dc hide]', openWith:'[dc hide]', closeWith:'[/dc]', className:"dcounter"},
			{name:'[dc show]', openWith:'[dc show]', closeWith:'[/dc]', className:"dcounter"},
			{separator:'---------------' },
			{name:'Справка', className:'help', beforeInsert:function(){ window.open('http://maxhub.ru/page/plugin-down-counter'); } },
		]},
EOF;
	return $args;
}


# получение опций со значениями по-умолчанию
function down_counter_get_options( $optskey = '', $refresh = false, $optsfile = 'settings.php' )
{
	static $options;
		
	if( !isset($optskey) || $optskey == '' ) $optskey = 'plugin_'.basename(dirname(__FILE__));
		
	if( !isset($options[$optskey]) || count($options[$optskey]) == 0 || $refresh )
	{
		$options[$optskey] = mso_get_option($optskey, 'plugins', array());
			
		if( $fn = mso_fe($optsfile, getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) require($fn);
		if( isset($settings) && count($settings) > 0 )
		{
			foreach( $settings as $key => $opt )
			{
				if( !isset($options[$optskey][$key]) && isset($opt['default']) ) $options[$optskey][$key] = $opt['default'];
			}
		}
	}
		
	return $options[$optskey];
}

# подключаем файл с функцией mso_admin_plugin_options2
if( $fn = mso_fe('mso-admin-plugin-options2.php', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) require($fn);

# end file