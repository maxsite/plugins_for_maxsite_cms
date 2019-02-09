<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Youtube_Previewer» for MaxSite CMS
 *
 * Author: (c) Илья Земсков http://vizr.ru/
 */

# функция автоподключения плагина
function youtube_previewer_autoload($args = array())
{
	mso_hook_add('admin_init', basename(dirname(__FILE__)).'_admin_init'); # хук для подключения плагина в меню админки
	mso_hook_add('admin_head', basename(dirname(__FILE__)).'_admin_head'); # хук для подключения стилей на внутренних страницах

	mso_hook_add('new_page', basename(dirname(__FILE__)).'_parse'); # хук на сохранение новой записи
	mso_hook_add('edit_page', basename(dirname(__FILE__)).'_parse'); # хук на редактирование записи
}

# функция выполняется при активации (вкл) плагина
function youtube_previewer_activate($args = array())
{
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow(basename(dirname(__FILE__)).'_edit', 'Админ-доступ к плагину «Youtube_Previewer»');
	mso_create_allow(basename(dirname(__FILE__)).'_options', 'Админ-доступ к опциям плагина «Youtube_Previewer»');

	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function youtube_previewer_deactivate($args = array())
{
	return $args;
}

# функция выполняется при деинсталяции плагина
function youtube_previewer_uninstall($args = array())
{
	# удалим созданные опции
	mso_delete_option('plugin_'.basename(dirname(__FILE__)), 'plugins');

	# удалим созданные разрешения
	mso_remove_allow(basename(dirname(__FILE__)).'_edit');
	mso_remove_allow(basename(dirname(__FILE__)).'_options');

	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function youtube_previewer_mso_options()
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_options') )
	{
		echo t('Доступ запрещен');
		return;
	}

	if( !function_exists('mso_get_ini_file') )
	{
		require_once( getinfo('common_dir') . 'inifile.php' ); # функции для работы с ini-файлом
	}

	if( !file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/options.ini') ) return;

	$prefs = mso_get_ini_file( getinfo('plugins_dir') . basename(dirname(__FILE__)) . '/options.ini'); # и свой файл опций

	$options = array();
	foreach( $prefs as $name => $params )
	{
		$options[ $params['options_key'] ][ ( isset($params['type']) && $params['type'] == 'info' ? 'title' : 'name' ) ] = $name;

		foreach( $params as $k => $param )
		{
			if( $k == 'section' )
			{
				$title = $param;
			}
			elseif( $k == 'section_description' )
			{
				$description = $param;
			}
			else
			{
				$options[ $params['options_key'] ][$k] = $param;
			}
		}
	}

	#pr($prefs);
	#pr($options);

	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-menu.php' ); # подключаем файл вывода меню

	# ключ, тип, ключи массива
	mso_admin_plugin_options( 'plugin_'.basename(dirname(__FILE__)), 'plugins', $options, $title, $description );
	
	if( isset($_POST) ) { mso_flush_cache(); }

	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}


# получение опций со значениями по-умолчанию
function youtube_previewer_get_options( $optskey = '', $refresh = false )
{
	static $options;

	if( !isset($optskey) || $optskey == '' ) $optskey = 'plugin_'.basename(dirname(__FILE__));

	if( !isset($options[$optskey]) || count($options[$optskey]) == 0 || $refresh )
	{
		$options[$optskey] = mso_get_option($optskey, 'plugins', array());

		if( !function_exists('mso_get_ini_file') )
		{
			require_once( getinfo('common_dir') . 'inifile.php' ); # функции для работы с ini-файлом
		}

		if( file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/options.ini') )
		{
			$prefs = mso_get_ini_file( getinfo('plugins_dir') . basename(dirname(__FILE__)) . '/options.ini'); # и свой файл опций

			$settings = array();
			foreach( $prefs as $name => $params )
			{
				$settings[ $params['options_key'] ]['type'] = $params['type'];
				if( isset($params['default']) )
				{
					$settings[ $params['options_key'] ]['default'] = $params['default'];
				}
			}
		}

		if( isset($settings) && count($settings) > 0 )
		{
			foreach( $settings as $key => $opt )
			{
				if( !isset($options[$optskey][$key]) && $opt['type'] != 'checkbox' && isset($opt['default']) ) $options[$optskey][$key] = $opt['default'];
			}
		}
	}

	return $options[$optskey];
}

# функция выполняется при указаном хуке admin_init
function youtube_previewer_admin_init($args = array())
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_edit') )
	{
		return $args;
	}

	# получаем опции
	$options = youtube_previewer_get_options();

	if( isset($options['search_panel']) and $options['search_panel'] )
	{
		mso_admin_menu_add('plugins', basename(dirname(__FILE__)), 'Youtube_Previewer');
	}

	mso_admin_url_hook(basename(dirname(__FILE__)), basename(dirname(__FILE__)).'_admin');

	return $args;
}

# функция подключения дополнительных стилей и скриптов для работы плагина в админке
function youtube_previewer_admin_head($args = array())
{
	# получаем опции
	$options = youtube_previewer_get_options();

	$plugin_url = getinfo('plugins_url').basename(dirname(__FILE__)).'/';

	#if( mso_segment(1) == 'admin' && mso_segment(2) == 'plugin_options' && mso_segment(3) == basename(dirname(__FILE__)) )
	#{
		# стили для страницы опций
		#if( $fn = mso_fe('options.css', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) echo mso_load_style( $plugin_url.'options.css' );
	#}

	if( mso_segment(1) == 'admin' && mso_segment(2) == basename(dirname(__FILE__)) )
	{
		# стили для админ-панели
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/backend.css" type="text/css" media="screen">'.NR;

		# Куда отправлять AJAX-запросы
		$ajax_path = getinfo('ajax').base64_encode('plugins/'.basename(dirname(__FILE__)).'/do-ajax.php');
		echo "
			<script type=\"text/javascript\">
				var ajax_path = '".$ajax_path."', base_url = '".getinfo('site_admin_url').basename(dirname(__FILE__))."';
			</script>
		";

		# jQuery код для кнопок
		echo '<script src="'.$plugin_url.'backend.js"></script>'.NR; # подключаем js для админки
	}

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function youtube_previewer_admin($args = array())
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_edit') )
	{
		echo 'Доступ запрещен';
		return $args;
	}

	# получаем опции
	$options = youtube_previewer_get_options();

	# если опции ещё не заданы
	#if( count($options) == 0 )
	#{
		#mso_redirect('admin/plugin_options/'.basename(dirname(__FILE__))); # перебрасываем на страницу настройки опций
	#}

	# выносим админские (backend) функции в отдельный файл
	if( mso_segment(3) == '' )
	{
		if( $fn = mso_fe('backend.php', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) require($fn);
	}
	else
	{
		mso_redirect('admin/'.basename(dirname(__FILE__)));
	}

	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}

# функция поиска ролика для заполнения мета-поля превьюшки
function youtube_previewer_parse($args = array())
{
	#pr($args);

	# получаем опции
	$options = youtube_previewer_get_options();
	
	if( isset($options['work_status']) and $options['work_status'] )
	{
		$CI = & get_instance();

		$CI->db->select('mp.page_id, mp.page_title, mp.page_slug, mp.page_content, mm.meta_value img');
		$CI->db->from('mso_page mp');
		$CI->db->join('mso_meta mm', 'mm.meta_id_obj = mp.page_id AND mm.meta_key = "image_for_page"', 'left');
		$CI->db->like('mp.page_id', $args[0]); # подставляем id записи
		$CI->db->where('mm.meta_value', '');
		$CI->db->like('mp.page_content', 'youtube.com/embed');
		$CI->db->limit(1);

		$qry = $CI->db->get(); #pr($CI->db->last_query());

		if( isset($qry) && is_object($qry) && $qry->num_rows() > 0 )
		{
			$res = $qry->result_array(); #pr($res);
			$pg = $res[0];
			$path = getinfo('uploads_dir').'_pages/'.$pg['page_id'].'/';

			preg_match_all('/<iframe(.*?)><\/iframe>/msi', $pg['page_content'], $ma);
			
			if( isset($ma[1]) and count($ma[1]) > 0 ) # в записи найден html код вставки ролика
			{
				foreach( $ma[1] as $code )
				{
					preg_match('/youtube.com\/embed\/(.*?)\"/msi', $code, $rid);
				
					if( isset($rid[1]) and $rid[1] )
					{
						require_once( getinfo('common_dir') . 'uploads.php' ); # функции загрузки 
						require_once( getinfo('common_dir') . 'meta.php' ); # функции работы мета

						# формируем - https://img.youtube.com/vi/<insert-youtube-video-id-here>/0.jpg
						$url = 'https://img.youtube.com/vi/'.$rid[1].'/0.jpg';
						$pict = strtolower($rid[1]).'.jpg'; # файл картинки на диске
						$preview = getinfo('uploads_url').'_pages/'.$pg['page_id'].'/'.$pict; # адрес превьюшки в папке uploads
						$img = '<img src="'.$preview.'" width=200>';
						
						# скачивание картинку
						file_put_contents($path.$pict, file_get_contents($url));

						$up_data = array();
						$up_data['full_path'] = $path.$pict;
						$up_data['file_path'] = $path;
						$up_data['file_name'] = $rid[1].'.jpg';
				
						$r = array();
						$r['userfile_mini'] = 1; // делать миниатюру
						$r['userfile_mini_size'] = (int) mso_get_option('size_image_mini', 'general', 150);
						$r['mini_type'] = mso_get_option('image_mini_type', 'general', 1);
						$r['prev_size'] = 100;
				
						mso_upload_mini($up_data, $r); // миниатюра 
						mso_upload_prev($up_data, $r); // превьюшка

						# сохраняем мета-поле записи
						$meta = mso_add_meta( 'image_for_page', $pg['page_id'], 'page', $preview );
							
						break; # выходим из цикла, т.к. нашли картинку
					}
				}
			}
		}
	}
		
	return $args;
}