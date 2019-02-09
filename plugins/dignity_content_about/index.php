<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net/
 */

# функция автоподключения плагина
function dignity_content_about_autoload($args = array())
{
	if ( is_type('page') )
	{
		mso_hook_add('content_end', 'dignity_content_about_content_end');
	}
}

# функция выполняется при деинсталяции плагина
function dignity_content_about_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_content_about', 'plugins'); // удалим созданные опции
	return $args;
}

function dignity_content_about_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_dignity_content_about', 'plugins', 
		array(
			'text' => array(
					'type' => 'textarea', 
					'name' => 'Об авторе (Можно использовать HTML):', 
					'description' => 'Несколько слов об авторе.', 
					'default' => ''
					),
			),
		'Об авторе', // титул
		'Введите информацию об авторе.'   // инфо
	);
}

# функции плагина
function dignity_content_about_content_end($args = array())
{
	
	$options = mso_get_option('plugin_dignity_content_about', 'plugins', array());
	
	// если текст введён, то выводим...
	$text = '';	
	if (!$options['text'])
	{
		$options['text'] = '';
	}
	else
	{
		$text = '<p style="padding:15px 0px 0px 0px;"><hr><strong>'
			. t('Об авторе:', __FILE__) . '</strong></p><p>' . $options['text'] . '</p><hr>';
	}
	
	echo $text;
	
	return $args;
}

# end file