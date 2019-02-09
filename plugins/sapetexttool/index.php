<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «SapeTextTool» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-sapetexttool
 */

# функция автоподключения плагина
function sapetexttool_autoload($args = array())
{	
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow('sapetexttool_edit', 'Админ-доступ к плагину «SapeTextTool»');
		
	mso_hook_add('admin_init', 'sapetexttool_admin_init'); # хук для подключения плагина в меню админки
	mso_hook_add('admin_head', 'sapetexttool_admin_head'); # хук для подключения стилей на внутренних страницах
}

# функция выполняется при деинсталяции плагина
function sapetexttool_uninstall($args = array())
{
	# удалим созданные разрешения
	mso_remove_allow('sapetexttool_edit');
		
	return $args;
}

# функция выполняется при указаном хуке admin_init
function sapetexttool_admin_init($args = array()) 
{	
	mso_admin_menu_add('plugins', 'sapetexttool', 'SapeTextTool');
	mso_admin_url_hook('sapetexttool', 'sapetexttool_admin');
		
	return $args;
}

# функция подключения дополнительных стилей и скриптов для работы плагина в админке
function sapetexttool_admin_head($args = array())
{
	if( mso_segment(1) == 'admin' )
	{
		# Стили	
		echo '
			<style>
				div.sidebar ul.admin-menu ul.admin-submenu li.admin-menu-sapetexttool a:before {
					content: "\f079";
				}
			</style>
		';
	}

	if( mso_segment(1) == 'admin' && mso_segment(2) == basename(dirname(__FILE__)) )
	{
		# общие стили для админ-панели
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/admin.min.css" type="text/css" media="screen">'.NR;
			
		# Куда отправлять AJAX-запросы
		$ajax_path = getinfo('ajax').base64_encode('plugins/'.basename(dirname(__FILE__)).'/do-ajax.php');
		echo "
			<script type=\"text/javascript\">
				var ajax_path = '".$ajax_path."'
			</script>
		";
			
		# jQuery код для кнопок
		echo '<script src="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/admin.panel.min.js"></script>'.NR; # подключаем js для админки
	}

	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function sapetexttool_admin($args = array()) 
{
	# выносим админские (backend) функции в отдельный файл
	require(getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-admin.php');
}

?>