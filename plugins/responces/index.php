<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function responces_autoload($args = array())
{
	mso_hook_add('admin_init', 'responces_admin_init'); # хук на админку
	mso_hook_add('custom_page_404', 'responces_custom_page_404'); # хук для подключения к шаблону
}

# функция выполняется при активации (вкл) плагина
function responces_activate($args = array())
{	
	mso_create_allow('responces_edit', t('Админ-доступ к книге отзывов и предложений'));
	
	$CI = & get_instance();	

	if ( !$CI->db->table_exists('responces')) // нет таблицы responces
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "responces (
		responces_id bigint(20) NOT NULL auto_increment,
		responces_ip varchar(255) NOT NULL default '',
		responces_browser varchar(255) NOT NULL default '',
		responces_date datetime default NULL,
		responces_approved bigint(20) NOT NULL default '0',
		responces_name varchar(255) NOT NULL default '',
		responces_text longtext,
		responces_title varchar(255) NOT NULL default '',
		responces_email varchar(255) NOT NULL default '',
		responces_icq varchar(255) NOT NULL default '',
		responces_site varchar(255) NOT NULL default '',
		responces_phone varchar(255) NOT NULL default '',
		responces_custom1 varchar(255) NOT NULL default '',
		responces_custom2 varchar(255) NOT NULL default '',
		responces_custom3 varchar(255) NOT NULL default '',
		responces_custom4 varchar(255) NOT NULL default '',
		responces_custom5 varchar(255) NOT NULL default '',
		PRIMARY KEY (responces_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
		
	return $args;
}


# функция выполняется при деинстяляции плагина
function responces_uninstall($args = array())
{	
	mso_delete_option('plugin_responces', 'plugins' ); // удалим созданные опции
	mso_remove_allow('responces_edit'); // удалим созданные разрешения
	
	// удалим таблицу
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('responces');

	return $args;
}

# функция выполняется при указаном хуке admin_init
function responces_admin_init($args = array()) 
{
	if ( !mso_check_allow('responces_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'responces'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Книга отзывов и предложений'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/responces
	mso_admin_url_hook ($this_plugin_url, 'responces_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function responces_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('responces_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Книга отзывов и предложений') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Книга отзывов и предложений') . ' - " . $args; ' );
	
	if ( mso_segment(3) == 'edit') require(getinfo('plugins_dir') . 'responces/edit.php');
	elseif ( mso_segment(3) == 'editone') require(getinfo('plugins_dir') . 'responces/editone.php');
	else require(getinfo('plugins_dir') . 'responces/admin.php');
}

# подключаем свой файл к шаблону
function responces_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_responces', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'responces'; 
	
	if ( mso_segment(1)==$options['slug'] ) 
	{
		require( getinfo('plugins_dir') . 'responces/responces.php' ); // подключили свой файл вывода
		return true; // выходим с true
	}

	return $args;
}

?>