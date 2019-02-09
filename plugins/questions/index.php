<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * THE DIGNITY
 * (c) http://maxsite.thedignity.biz/
 */


# функция автоподключения плагина
function questions_autoload($args = array())
{
	mso_create_allow('questions_edit', t('Админ-доступ к ответам', __FILE__));
	mso_hook_add('admin_init', 'questions_admin_init'); # хук на админку
	mso_hook_add('custom_page_404', 'questions_custom_page_404'); # хук для подключения к шаблону
}

# функция выполняется при активации (вкл) плагина
function questions_activate($args = array())
{	
	$CI = & get_instance();	

	if ( !$CI->db->table_exists('questions')) // нет таблицы questions
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "questions (
		questions_id bigint(20) NOT NULL auto_increment,
		questions_ip varchar(255) NOT NULL default '',
		questions_date datetime default NULL,
		questions_approved bigint(20) NOT NULL default '0',
		questions_name varchar(255) NOT NULL default '',
		questions_text longtext,
		questions_email varchar(255) NOT NULL default '',
		questions_age varchar(255) NOT NULL default '',
		questions_city varchar(255) NOT NULL default '',
		questions_answer longtext,
		PRIMARY KEY (questions_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
		
	return $args;
}


# функция выполняется при деинстяляции плагина
function questions_uninstall($args = array())
{	
	mso_delete_option('plugin_questions', 'plugins'); // удалим созданные опции
	mso_remove_allow('questions_edit'); // удалим созданные разрешения
	
	// удалим таблицу
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('questions');

	return $args;
}

# функция выполняется при указаном хуке admin_init
function questions_admin_init($args = array()) 
{
	if ( !mso_check_allow('questions_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'questions'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Ответы', __FILE__));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/questions
	mso_admin_url_hook ($this_plugin_url, 'questions_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function questions_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('questions_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Ответы', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Ответы', __FILE__) . ' - " . $args; ' );
	
	if ( mso_segment(3) == 'edit') require(getinfo('plugins_dir') . 'questions/edit.php');
	elseif ( mso_segment(3) == 'editone') require(getinfo('plugins_dir') . 'questions/editone.php');
	else require(getinfo('plugins_dir') . 'questions/admin.php');
}

# подключаем свой файл к шаблону
function questions_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_questions', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'questions'; 
	
	if ( mso_segment(1)==$options['slug'] ) 
	{
		require( getinfo('plugins_dir') . 'questions/questions.php' ); // подключили свой файл вывода
		return true; // выходим с true
	}

	return $args;
}

?>
