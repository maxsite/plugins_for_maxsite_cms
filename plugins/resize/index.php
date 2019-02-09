<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function resize_autoload()
{
    mso_hook_add( 'head', 'resize_head');
    mso_hook_add( 'body_start', 'resize_show');
}

# функция выполняется при активации (вкл) плагина
function resize_activate($args = array())
{	
	mso_create_allow('resize_edit', t('Админ-доступ к настройкам resize'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function resize_deactivate($args = array())
{	
	// mso_delete_option('plugin_%%%', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function resize_uninstall($args = array())
{	
	mso_delete_option('plugin_resize', 'plugins' ); // удалим созданные опции
	mso_remove_allow('resize_edit'); // удалим созданные разрешения
	return $args;
}

# функции подключение стилей и скриптов
function resize_head()
{

  echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'resize/css/style.css" type="text/css">';
  echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'resize/js/workscript.js"></script>';

}

# функции вывода блока
function resize_show()
{
    // Информационный блок
    echo '<div id="resize-info" class="resize-info"></div>';

}

# end file