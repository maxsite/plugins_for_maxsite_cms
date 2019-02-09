<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
		

	
# функция автоподключения плагина
function other_comments_autoload($args = array())
{	
	mso_create_allow('other_comments_edit', t('Админ-доступ к настройкам other_comments', 'plugins'));
	mso_hook_add( 'admin_init', 'other_comments_admin_init');
//	mso_hook_add( 'head', 'other_comments_head');
	mso_hook_add( 'custom_page_404', 'other_comments_custom_page_404');
}

function other_comments_head($arg = array())
{
//	echo '<link type="text/css" rel="stylesheet" href="' . getinfo('plugins_url') . 'other_comments/style.css" media="screen" />';
	return $arg;
}

function other_comments_activate($args = array())
{	
	// создание таблиц
	$CI = get_instance();
							
	// виды (kinds) комментируемых сущностей. Однозначно определяются 1-м сегментом url-а
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'kinds` (
								`kind_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`kind_slug` text CHARACTER SET utf8,
								`kind_title` text CHARACTER SET utf8,
								`kind_desc` text CHARACTER SET utf8,
								`kind_custom` text CHARACTER SET utf8,
								PRIMARY KEY (`kind_id`),
								KEY kind_slug (kind_slug)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

  // таблица комментируемых сущностей (elements) для связи с комментируемыми сущностями
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'elements` (
								`element_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`element_kind_id` bigint(20),
								`element_slug` text CHARACTER SET utf8,
								`element_title` text CHARACTER SET utf8,
								`element_table_name` text CHARACTER SET utf8, 
								`element_id_in_table` text CHARACTER SET utf8,
								`element_comment_allow` bigint(20) NOT NULL default 1,
								PRIMARY KEY (`element_id`),
								KEY element_id_in_table (element_id_in_table),
								KEY element_slug (element_slug),
								KEY element_table_name (element_table_name)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
							
	// таблица комментов для сущностей
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'other_comments` (
								`comments_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`comments_element_id` bigint(20) NOT NULL,
								`comments_parent_id` bigint(20) NOT NULL,
								`comments_users_id` bigint(20) NOT NULL, 
								`comments_comusers_id` bigint(20) NOT NULL, 
								`comments_author_name` varchar(255) CHARACTER SET utf8 NOT NULL,
								`comments_author_ip` varchar(100) CHARACTER SET utf8 NOT NULL,
								`comments_date` datetime,
								`comments_content` text CHARACTER SET utf8,
								`comments_rating` int(11) default 0,
								`comments_approved` int(11)  NOT NULL default 0,
								PRIMARY KEY (`comments_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
							
							
	return $args;
}

function other_comments_uninstall($args = array())
{	
  $CI = & get_instance(); // вот здесь мы и получаем доступ к CodeIgniter
	$CI->load->dbforge() ; // подгружаем библиотеку фордж, для создания таблиц
	$CI->dbforge->drop_table('other_comments', TRUE); 
	$CI->dbforge->drop_table('elements', TRUE); 
	$CI->dbforge->drop_table('kinds', TRUE); 
	mso_delete_option_mask('other_comments_last_widget_', 'plugins'); // удалим созданные опции
	mso_remove_allow('other_comments_edit'); // удалим созданные разрешения
	
	return $args;
}

# выводит меню в админке
function other_comments_admin_init($args = array()) 
{
	if ( !mso_check_allow('other_comments_edit') ) 
	{
		return $args;
	}
	$this_plugin_url = 'other_comments'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, 'Other Comments');
	mso_admin_url_hook ($this_plugin_url, 'other_comments_admin_page');
	return $args;
}




function other_comments_admin_page( $args = array() )
{
	if ( !mso_check_allow('%%%_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	$plugin_url = getinfo('site_admin_url') . 'other_comments';

	$menu  = mso_admin_link_segment_build($plugin_url, 'comments', t('Комментарии', __FILE__), 'select') . ' | ';
	$menu .= mso_admin_link_segment_build($plugin_url, 'kinds', t('Виды сущностей', __FILE__), 'select') . ' | ';	
	$menu  .= mso_admin_link_segment_build($plugin_url, 'elements', t('Сущности', __FILE__), 'select') . ' | ';

	echo $menu;

	$seg = mso_segment(3);
	require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

	if ($seg == 'comment-edit')
	{
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Редактирование комментария", "admin"); ' );
		mso_hook_add_dinamic( 'admin_title', ' return t("Редактирование комментария", "admin") . " - " . $args; ' );
		require(getinfo('plugins_dir') . 'other_comments/admin/comment-edit.php');
	}
	else if ($seg == 'kinds')
	{
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Редактирование видов сущностей", "admin"); ' );
		mso_hook_add_dinamic( 'admin_title', ' return t("Редактирование вида сущности", "admin") . " - " . $args; ' );
		require(getinfo('plugins_dir') . 'other_comments/admin/kinds.php');
	}	
	else if ($seg == 'elements')
	{
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Редактирование сущностей", "admin"); ' );
		mso_hook_add_dinamic( 'admin_title', ' return t("Редактирование сущности", "admin") . " - " . $args; ' );
		require(getinfo('plugins_dir') . 'other_comments/admin/elements.php');
	}		
	else {
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Комментарии', 'admin') . '"; ' );
		mso_hook_add_dinamic( 'admin_title', ' return "' . t('Комментарии', 'admin') . ' - " . $args; ' );
		require(getinfo('plugins_dir') . 'other_comments/admin/comments.php');
	}
	return $args;
}



function other_comments_last_widget($num = 1) 
{
	$widget = 'other_comments_last_foto_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';	
	// получить другие опции	
	
	return other_comments_last_foto_widget_custom($options, $num);	
}


function other_comments_last_widget_form($num = 1) 
{
	$widget = 'other_comments_last_foto_widget_' . $num;
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


function other_comments_last_widget_update($num = 1) 
{
	$widget = 'other_comments_last_foto_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');	
}


function other_comments_last_widget_custom($options = array(), $num = 1)
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


?>