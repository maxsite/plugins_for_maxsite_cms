<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function redirect_404_autoload()
{
	mso_hook_add( 'page_404', 'redirect_404_custom');
}

# функция выполняется при активации (вкл) плагина
function redirect_404_activate($args = array())
{
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function redirect_404_deactivate($args = array())
{
	return $args;
}

# функция выполняется при деинстяляции плагина
function redirect_404_uninstall($args = array())
{
	mso_delete_option('plugin_redirect_404', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function redirect_404_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_redirect_404', 'plugins', 
		array(
			'redirect_404' => array(
							'type' => 'text', 
							'name' => 'Куда редиректить',
							'description' => 'По этому адресу отправляем, если не найдена запрошенная страница, категория и т.п.<br />Если пусто — не отправляем никуда.<br />Заголовок 404 зависит от настроек шаблона.',
							'default' => ''
						),
			'text_404'    => array(
							'type' => 'textarea', 
							'name' => 'Что писать',
							'description' => 'Если никуда не перенаправляем, то может быть хотим что-то сказать на текущей 404 странице.',
							'default' => '&nbsp;'
						),
			),
		'Настройки плагина redirect_404', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function redirect_404_custom($arg = array())
{
	$options = mso_get_option('plugin_redirect_404', 'plugins', array() ); // получаем опции
	$r = isset($options['redirect_404']) ? trim($options['redirect_404']) : '';
	if (!$options['text_404']) $options['text_404'] = '&nbsp;';

	if ($r <> '') mso_redirect($r, true);
	return $options['text_404'];
}

?>