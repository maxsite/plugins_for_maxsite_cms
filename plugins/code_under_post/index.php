<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function code_under_post_autoload()
{
	//mso_create_allow('code_under_post_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('code_under_post', __FILE__));
	if ( is_type('page') )
	{
		mso_hook_add('content_end','code_under_post_content_end');
		mso_hook_add('head','code_under_post_head');
	}
}

# функция выполняется при активации (вкл) плагина
function code_under_post_activate($args = array())
{	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function code_under_post_deactivate($args = array())
{	
	// mso_delete_option('plugin_code_under_post', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function code_under_post_uninstall($args = array())
{	
	mso_delete_option('plugin_code_under_post', 'plugins'); // удалим созданные опции
	// mso_remove_allow('code_under_post_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function code_under_post_mso_options() 
{
	/*
	if ( !mso_check_allow('code_under_post_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}
	*/
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_code_under_post', 'plugins', 
		array(
			'head' => array(
							'type' => 'textarea', 
							'name' => t('Вставка в заголовок', __FILE__), 
							'description' => t('Код, вставляемый в заголовок (&lt;head&gt;), часто это скрипты и стили CSS.', __FILE__), 
							'default' => '<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?20"></script><script type="text/javascript">VK.init({apiId: 2138144, onlyWidgets: true});</script>'
						),
			'content_end' => array(
							'type' => 'textarea', 
							'name' => t('Вставка в тело, после поста', __FILE__), 
							'description' => t('Отображаемый код, вставляемый в тело страницы (&lt;body&gt;), часть кода, которая должна отображаться после контента.', __FILE__), 
							'default' => '<div class="vkshare"><div id="vk_like"></div>
<script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "full"});
</script></div>'
						),
			),
			
		'Настройки плагина code_under_post', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function code_under_post_head() //вставка в заголовок
{
	$options = mso_get_option('plugin_code_under_post', 'plugins', array('head' => 'tanderror'));
	echo $options['head'];
}

function code_under_post_content_end() //вставка после поста
{
	$options = mso_get_option('plugin_code_under_post', 'plugins', array());
	echo $options['content_end'];
}
?>