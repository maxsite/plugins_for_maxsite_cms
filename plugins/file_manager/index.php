<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function file_manager_autoload($args = array())
{	
	mso_create_allow('file_manager',  t('Админ-доступ к менеджеру файлов (плагин)', 'admin'));
	mso_hook_add( 'admin_init', 'file_manager_admin_init');
	mso_hook_add( 'admin_head', 'file_manager_admin_head');
	
	 mso_hook_add( 'forum_comments_content_end', 'file_manager_comments'); // для вставки формы загрузки по хуку редактора форума 
	
	// mso_hook_add( 'comments_content_end', 'file_manager_comments'); // для формы загрузки изображений в комментарий
		
	// mso_hook_add( 'admin_page_form_add_all_meta', 'file_manager_meta'); // для вставки загрузчика в секцию метаполей
	
	// mso_hook_add( 'editor_custom', 'file_manager_editor_posle'); // для вставки загрузчика после редактора
	
	 mso_hook_add( 'admin_page_form_q_files', 'file_manager_q_files'); // для вставки загрузчика перед метаполями в секцию загрузок

}


// для подключения в секцию метаполей
function file_manager_meta($args = array()) 
{
  require (getinfo('plugins_dir') . 'file_manager/manager.php');
  return file_manager_form();
}


// для подключения после редактора
function file_manager_editor_posle($args = array()) 
{
  require (getinfo('plugins_dir') . 'file_manager/manager.php');
 
 $out = file_manager_form();

  $args['posle'] = $out . $args['posle'];
  return $args;
}

// для подключения в секцию загрузок
function file_manager_q_files($args = array()) 
{
  require (getinfo('plugins_dir') . 'file_manager/manager.php');
 
 $out = file_manager_form();

  return $out;
}

// для подключения в комментарии
function file_manager_comments($args = array()) 
{
	 //загрузка изображений пользователями
  require(getinfo('plugins_dir') . 'file_manager/manager-comuser.php');
	echo file_manager_comuser();
  return $args;
}


function file_manager_admin_head($args = array()) 
{
	echo mso_load_jquery('alerts/jquery.alerts.js');
	echo mso_load_jquery('cornerz.js');
	echo '	<link href="' . getinfo('common_url') . 'jquery/alerts/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen">';

	return $args;
}

# функция выполняется при указаном хуке admin_init
function file_manager_admin_init($args = array()) 
{	
	if ( !mso_check_allow('file_manager') ) 
	{
		return $args;
	}

	$this_plugin_url = 'file_manager'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, '' . t('Менеджер файлов', 'admin') . '');
	mso_admin_url_hook ($this_plugin_url, 'file_manager_admin');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function file_manager_admin($args = array()) 
{
	if ( !mso_check_allow('file_manager') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	# выносим админские функции отдельно в файл
	global $MSO;
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Загрузки. Файлы. Галереи', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Загрузки. Файлы. Галереи', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . 'file_manager/admin.php');
}

function file_manager_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_file_manager', 'plugins', 
		array(
			'allowed_types' => array(
						'type' => 'text', 
						'name' => 'Разрешенные типы файлов', 
						'description' => 'Список типов файлов, которые можно загружать через плагин (типы указывать через |).',
						'default' => 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz'
			),
			'hide_options' => array(
						'type' => 'select',
						'name' => t('Прятать настройки в спойлер', 'plugins'),
						'description' => t('Прячет настройки обработки картинок и создания/удаления папок под спойлер', 'plugins'),
						'values' => t('1||Да #2||Нет', 'plugins'),
						'default' => '1'					
			),
			'tree_expand' => array(
						'type' => 'select',
						'name' => t('Разворачивать дерево', 'plugins'),
						'description' => t('При навигации по папкам дерево всегда полностью разверачивается. При смене настройки обязательно нужно очистить кэш', 'plugins'),
						'values' => t('1||Да #2||Нет', 'plugins'),
						'default' => '1'					
			),
			'show_size' => array(
						'type' => 'select',
						'name' => t('Показывать размер', 'plugins'),
						'description' => t('Показывать размер файлов возле имени?', 'plugins'),
						'values' => t('1||Да #2||Нет', 'plugins'),
						'default' => '1'					
			),
			'userfile_resize_size' => array(
						'type' => 'text', 
						'name' => 'Ресайз для комюзеров', 
						'description' => 'Укажите размер изображения до которого будет ограничиваться при загрузке комюзером.',
						'default' => '800'				
			),			
			'userfile_mini' => array(
						'type' => 'select',
						'name' => t('Делать миниатюру комюзерам', 'plugins'),
						'description' => t('Делать миниатюру комюзерам?', 'plugins'),
						'values' => t('1||Да #2||Нет', 'plugins'),
						'default' => '1'					
			),		
			'userfile_mini_size' => array(
						'type' => 'text', 
						'name' => 'Миниатюра для комюзеров', 
						'description' => 'Укажите размер миниатюры в mini/.',
						'default' => '200'					
			),						
		),
		'Настройки плагина "Менеджер файлов"', // титул
		'Укажите необходимые опции.'   // инфо
	);
}


?>