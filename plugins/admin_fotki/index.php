<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
global $plug_url; 

		
//$plug_url = 'admin_fotki';

$current_dir = pathinfo( __FILE__ );
$current_dir = $current_dir['dirname'];
$current_dir = str_replace( '\\', '/', $current_dir);
$pos = strrpos( $current_dir, '/');
$current_dir = substr( $current_dir, $pos + 1, strlen( $current_dir ) - $pos );
$plug_url = $current_dir;

	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	global $foto_dir;
	$foto_dir = $options['upload_path'];
	
	if ( !isset($options['url_page_foto']) ) $options['url_page_foto'] = 'foto';
	global $foto_url;
	$foto_url = $options['url_page_foto'];
	
	if ( !isset($options['url_page_album']) ) $options['url_page_album'] = 'albums';
	global $foto_albums;
	$foto_albums = $options['url_page_album'];

	if ( !isset($options['url_page_gallery']) ) $options['url_page_gallery'] = 'album';
	global $foto_gallery;
	$foto_gallery = $options['url_page_gallery'];

	
# функция автоподключения плагина
function admin_fotki_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_fotki_init');
	mso_hook_add( 'head', 'admin_fotki_head');
	mso_hook_add( 'custom_page_404', 'admin_fotki_custom_page_404');
	mso_register_widget('admin_fotki_last_foto_widget', 'Новые фотографии'); 
	mso_register_widget('admin_fotki_album_list_widget', 'Альбомы'); 
}

function admin_fotki_head($arg = array())
{
	//session_start();
	global $plug_url;
	echo '<link type="text/css" rel="stylesheet" href="' . getinfo('plugins_url') . $plug_url . '/style.css" media="screen" />';
	echo '<script type="text/javascript" src="' . getinfo('plugins_url') . $plug_url . '/fotki.js" ></script>';
	
		echo '<script type="text/javascript">
			var path_ajax_fotki = "' . getinfo('ajax') . base64_encode('plugins/' . $plug_url . '/admin-ajax.php') . '";
		</script>';		
	
	return $arg;
}

function admin_fotki_activate($args = array())
{	
	// создание таблиц
	// таблица фоток
	$CI = get_instance();
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'foto` (
								`foto_id` int(11) NOT NULL AUTO_INCREMENT,
								`foto_album_id` int(11) NOT NULL,
								`foto_title` text CHARACTER SET utf8 NOT NULL,
								`foto_slug` text CHARACTER SET utf8 NOT NULL,
								`foto_descr` text CHARACTER SET utf8 NOT NULL,
								`foto_exif` text CHARACTER SET utf8 NOT NULL,
								`foto_path` text CHARACTER SET utf8 NOT NULL,
								`foto_seo_title` text CHARACTER SET utf8 NOT NULL,
								`foto_seo_descr` text CHARACTER SET utf8 NOT NULL,
								`foto_seo_meta` text CHARACTER SET utf8 NOT NULL,
								`foto_date` datetime NOT NULL,
								`foto_view_count` int(11) default 0,
								`foto_rate_plus` int(11) default 0,
								`foto_rate_minus` int(11) default 0,
								`foto_rate_count` int(11) default 0,
								PRIMARY KEY (`foto_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ');

	
	// таблица меток для фоток
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'foto_tags` (
								`foto_tag_id` int(11) NOT NULL AUTO_INCREMENT,
								`foto_id` int(11) NOT NULL,
								`foto_tag_name` text CHARACTER SET utf8 NOT NULL,
								PRIMARY KEY (`foto_tag_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
							
	// таблица альбомов для фоток
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'foto_albums` (
								`foto_album_id` int(11) NOT NULL AUTO_INCREMENT,
								`foto_album_title` text CHARACTER SET utf8 NOT NULL,
								`foto_album_slug` text CHARACTER SET utf8 NOT NULL,
								`foto_album_picture` text CHARACTER SET utf8 NULL,
								`foto_album_parent_id` int(11) NOT NULL,
								`foto_album_date` datetime NOT NULL,
								`foto_album_password` text CHARACTER SET utf8 NULL,
								PRIMARY KEY (`foto_album_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

	// таблица комментов для фоток
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'foto_comments` (
								`foto_comments_id` int(11) NOT NULL AUTO_INCREMENT,
								`foto_comments_foto_id` int(11) NOT NULL,
								`foto_comments_parent_id` int(11) NOT NULL,
								`foto_comments_users_id` int(11) NOT NULL, 
								`foto_comments_comusers_id` int(11) NOT NULL, 
								`foto_comments_author_name` varchar(255) CHARACTER SET utf8 NOT NULL,
								`foto_comments_author_ip` varchar(100) CHARACTER SET utf8 NOT NULL,
								`foto_comments_date` datetime NOT NULL,
								`foto_comments_content` text CHARACTER SET utf8,
								`foto_comments_rating` int(11) default 0,
								`foto_comments_approved` int(11) NOT NULL,
								PRIMARY KEY (`foto_comments_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
							
	// проверим альбом "Неразобранное"
	$CI->db->select('*');
	$CI->db->from('foto_albums');
	$CI->db->where('foto_album_id', 1);
	$CI->db->where('foto_album_title', 'Неразобранное');
	$CI->db->where('foto_album_slug', 'nerazobrannoe');
	$query = $CI->db->get();
	if ($query->num_rows() <= 0)
	{
		// вставим данные
		$data = array('foto_album_title' => 'Неразобранное',
		              'foto_album_slug' => 'nerazobrannoe',
					  'foto_album_parent_id' => 0,
					  'foto_album_date' => 'NOW()');
		$CI->db->insert( 'foto_albums', $data );			  
	}
	return $args;
}

function admin_fotki_uninstall($args = array())
{	
	mso_delete_option_mask('admin_fotki_last_foto_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option_mask('admin_fotki_album_list_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# выводит меню в админке
function admin_fotki_init($args = array()) 
{
	mso_admin_menu_add('fotki', '', t('Фотки', 'admin'), 3);
	
	mso_hook_add('admin_head', 'admin_fotki_admin_head');
	
	// добавить фотку
	$this_plugin_url = 'add-foto';
	mso_admin_menu_add( 'fotki', $this_plugin_url, t('Добавить новую', 'admin'), 1);
	mso_admin_url_hook ('add-foto', 'admin_fotki_add_foto');
	
	$this_plugin_url = 'edit-foto'; // url и hook
	mso_admin_url_hook ($this_plugin_url, 'admin_fotki_edit_foto');
		
	// просмотр фоток
	$this_plugin_url = 'show-foto';
	mso_admin_menu_add( 'fotki', $this_plugin_url, t('Просмотр', 'admin'), 2);
	mso_admin_url_hook ('show-foto', 'admin_fotki_show_foto');
	
	// просмотр альбомов - аналогично рубрикам
	$this_plugin_url = 'show-album';
	mso_admin_menu_add( 'fotki', $this_plugin_url, t('Альбомы', 'admin'), 3);
	mso_admin_url_hook ('show-album', 'admin_fotki_show_album');

	// общие настройки
	$this_plugin_url = 'foto-options';
	mso_admin_menu_add( 'fotki', $this_plugin_url, t('Настройки', 'admin'), 4);
	mso_admin_url_hook ('foto-options', 'admin_fotki_options_foto');

	// администрирование комментариев
	$this_plugin_url = 'foto-comments';
	mso_admin_menu_add( 'fotki', $this_plugin_url, t('Комментарии', 'admin'), 4);
	mso_admin_url_hook ('foto-comments', 'admin_fotki_comments');
	
	
	return $args;
}

function admin_fotki_add_foto($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	global $plug_url;
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Новая фотография', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Новая фотография', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . $plug_url . '/add-foto.php');
	return $args;
}

function admin_fotki_edit_foto($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	global $plug_url;
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Редактировании фотографии', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Редактировании фотографии', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . $plug_url . '/edit-foto.php');
	return $args;
}

function admin_fotki_show_foto( $args = array() )
{
	global $MSO;
	global $plug_url;
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Список фотографий', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Список фотографий', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . $plug_url . '/show-foto.php');
	return $args;
}

function admin_fotki_show_album( $args = array() )
{
	global $MSO;
	global $plug_url;
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Список альбомов', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Список альбомов', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . $plug_url . '/show-album.php');
	return $args;
}

function admin_fotki_options_foto( $args = array() )
{
	global $MSO;
	global $plug_url;
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Настройки', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Настройки', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . $plug_url . '/foto-options.php');
	return $args;
}

function admin_fotki_comments( $args = array() )
{
	global $MSO;
	global $plug_url;

	$seg = mso_segment(3);
	
	if ($seg == 'foto-comment-edit')
	{
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Редактирование комментария", "admin"); ' );
		mso_hook_add_dinamic( 'admin_title', ' return t("Редактирование комментария", "admin") . " - " . $args; ' );
		require($MSO->config['plugins_dir'] . $plug_url . '/foto-comment-edit.php');
	} else {
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Комментарии', 'admin') . '"; ' );
		mso_hook_add_dinamic( 'admin_title', ' return "' . t('Комментарии', 'admin') . ' - " . $args; ' );
		require($MSO->config['plugins_dir'] . $plug_url . '/foto-admin-comments.php');
	}
	return $args;
}


function admin_fotki_admin_head( $args = array() ) {
	
	global $plug_url;
	$seg = mso_segment(2);
	if ( ($seg == 'show-album') | ($seg == 'show-foto') | ($seg == 'add-foto') | ($seg == 'edit-foto') | ($seg == 'foto-options')) 
	{
		echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . $plug_url . '/style.css" type="text/css" >';
		echo '<script type="text/javascript" src="' . getinfo('plugins_url') . $plug_url . '/admin.js" />';
		echo '<script type="text/javascript">
			var path_ajax_fotki = "' . getinfo('ajax') . base64_encode('plugins/' . $plug_url . '/admin-ajax.php') . '";
		</script>';		
	}	
	return $args;
}

function admin_fotki_last_foto_widget($num = 1) 
{
	$widget = 'admin_fotki_last_foto_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';	
	// получить другие опции	
	
	return admin_fotki_last_foto_widget_custom($options, $num);	
}

function admin_fotki_album_list_widget($num = 1) 
{
	$widget = 'admin_fotki_album_list_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';	
		
	// получить другие опции	
	
	return admin_fotki_album_list_widget_custom($options, $num);	
		
}

function admin_fotki_last_foto_widget_form($num = 1) 
{
	$widget = 'admin_fotki_last_foto_widget_' . $num;
	$options = mso_get_option($widget, 'plugins', array());
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) )  $options['count'] = 3;
	
	// вывод самой формы
	$CI = get_instance();
	$CI->load->helper('form');
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Кол-во:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	return $form;	
	
}

function admin_fotki_album_list_widget_form($num = 1) 
{
	$widget = 'admin_fotki_album_list_widget_' . $num;
	$options = mso_get_option($widget, 'plugins', array());
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) )  $options['count'] = 3;
	
	// вывод самой формы
	$CI = get_instance();
	$CI->load->helper('form');
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Кол-во:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;	
	return $form;	
	
}

function admin_fotki_last_foto_widget_update($num = 1) 
{
	$widget = 'admin_fotki_last_foto_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');	
}

function admin_fotki_album_list_widget_update($num = 1) 
{
	$widget = 'admin_fotki_album_list_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');	
}


function admin_fotki_last_foto_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) 	$options['count'] = 3;
	
	$out = '';
	global $plug_url;
	global $foto_url;
	global $foto_dir;
	global $foto_gallery;
	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );
	$res = get_fotos($options['count'], false, 'desc' );
	if ( is_array($res) ) {
		foreach ( $res as $foto ) {
			extract( $foto );
			$out .= '<div class="last-foto-widget">';
			//pr( $foto );
			$url = getinfo('site_url') . $foto_url . '/' . $foto_slug;
			$img = getinfo('uploads_url') . $foto_dir . '/mini/' . $foto_path;
			$out .= '<a href="'.$url.'" title="'.$foto_title.'"><img src="'.$img.'" alt="'.$foto_title.'" /></a>';
			$out .= '</div>';
		}
		
		$out .= '<div class="foto-all"><a href="'.getinfo('site_url').  $foto_gallery . '" title="Все фотографии">Все фотографии</a></div>';
	}
	if ($out and $options['header']) $out = $options['header'] . $out;
	
	return $out;	
}

function admin_fotki_album_list_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) 	$options['count'] = 3;
	
	$out = '';
	global $plug_url;
	global $foto_url;
	global $foto_dir;
	//global $foto_album;
	global $foto_albums;

	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );
	$res = get_albums($options['count'], 0, 'desc' );
	if ( is_array($res) ) {
		$out .= '<ul class="is_link">';
		foreach ( $res as $album ) {
			extract( $album );
			//pr( $foto );
			$url = getinfo('site_url') . $foto_albums . '/' . $foto_album_slug;
			//$img = getinfo('uploads_url') . $foto_dir . '/mini/' . $foto_path;
			$out .= '<li><a href="'.$url.'">'.$foto_album_title.'</a></li>';
		}
		$out .= '</ul>';
		$out .= '<div class="album-all"><a href="'.getinfo('site_url').  $foto_albums . '">Все альбомы</a></div>';
	}
	
	if ($out and $options['header']) $out = $options['header'] . $out;
	
	return $out;	

}

function admin_fotki_custom_page_404($args=false) {
	
	$segment = mso_segment(1);
	
	global $plug_url;
	global $foto_url;
	global $foto_albums;
	global $foto_gallery;
	
	if ( $segment == $foto_url ) {
		require(getinfo('plugins_dir'). $plug_url . '/foto.php');
		return true;
	}
	else if ( $segment == $foto_albums ) {
		require(getinfo('plugins_dir'). $plug_url . '/albums.php');
		return true;
	} 
	else if ( $segment == $foto_gallery ) {
		require(getinfo('plugins_dir'). $plug_url . '/album.php');
		return true;
	} else
	return $args;
}

function get_last_thumb_fotos($count = 7, $html_do = '', $html_posle = '', $sort_order = 'desc', $exclude_foto = false, $from_album = false,
                              $custom_class = 'last-foto' ) 
{
	$out = '';
	if ( $count < 1 ) return $out;
	global $plug_url;
	global $foto_url;
	global $foto_dir;
	global $foto_gallery;
	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );
	$res = get_fotos($count, false, $sort_order, 0, $exclude_foto, $from_album );
	if ( is_array($res) ) {
		$out .= $html_do;
		foreach ( $res as $foto ) {
			extract( $foto );
			$out .= '<div class="'.$custom_class.'">';
			//pr( $foto );
			$url = getinfo('site_url') . $foto_url . '/' . $foto_slug;
			$img = getinfo('uploads_url') . $foto_dir . '/mini/' . $foto_path;
			$out .= '<a href="'.$url.'" title="'.$foto_title.'"><img src="'.$img.'" alt="'.$foto_title.'" /></a>';
			$out .= '</div>';
		}
		$out .= $html_posle;
	}
	return $out;	
}

function get_rnd_big_foto () {
	global $plug_url;
	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );
	$res = get_fotos(1, false, 'random' );
	if ( isset( $res[0] ) ) return $res[0]; else return false;	
	
}

/* function get_foto_view_count( $fotoid ) {

} */

function get_foto_comments_count( $fotoid ) {
	$CI = get_instance();
	$cnt = 0;
	if ( $fotoid !== false ) 
	{
		$CI->db->where('foto_comments_foto_id', $fotoid);
		$CI->db->from('foto_comments');
		$cnt = $CI->db->count_all_results();	
	}	
	return $cnt;
	
}

	function check_allready_vote ( $fotoid ) {
				// определим, голосовал ли уже?
				$name_cookies = 'maxsite_fotki_rate';
				$expire = 60 * 60 * 24 * 30 * 12; // 1 год  -----------30 дней = 2592000 секунд
					  
				// через сессии


				if (isset($_SESSION[$name_cookies]))	$all_pages = $_SESSION[$name_cookies]; // значения текущего кука
				else $all_pages = ''; // нет такой куки вообще
	
				$all_pages = explode(' ', $all_pages); // разделим в массив
				if ( in_array($fotoid, $all_pages) )
				{
					// вы уже голосовали
					//echo 'allready_vote';
					return true;
				} else return false;
	}
?>