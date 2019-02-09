<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина

function adscut_autoload($args = array())
{
	mso_create_allow('adscut_edit', 'Админ-доступ к редактированию adscut');
	mso_hook_add( 'init', 'adscut_init'); # хук на инициализацию
	mso_hook_add( 'admin_init', 'adscut_admin_init'); # хук на админку
}


# функция выполняется при деинсталяции плагина
function adscut_uninstall($args = array())
{	
	mso_delete_option_mask('adscut_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function adscut_admin_init($args = array()) 
{
	if ( mso_check_allow('plugin_adscut') ) 
	{
		$this_plugin_url = 'plugin_adscut'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, 'AdsCUT');
		mso_admin_url_hook ($this_plugin_url, 'adscut_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function adscut_admin_page($args = array()) 
{
	global $MSO;
	
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_adscut') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройка AdsCUT"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройка AdsCUT - " . $args; ' );
	require($MSO->config['plugins_dir'] . 'adscut/admin.php');
}


# инициализируем обработку изначального текста
function adscut_init($args = array()) 
{
	global $adscut, $MSO;
	
	$options = mso_get_option('adscut', 'plugins', array() ); // получаем опции
	
	if (isset($options['ushko']) 
		and isset($options['start']) and $options['start']) // можно подключать
	{
			mso_hook_add( 'content_out', 'adscut_content'); # хук на начальный текст для вывода
			mso_hook_add( 'content_complete', 'adscut_content_last'); # хук на обработанный всеми фильтрами текст для вывода
	}
	
	return $args;
}




# преформатирование
function adscut_content($text = '')
{
//	$text = preg_replace('/\[cut(.*?)?\]/','[cut][ADS]',$text);
//	$text = preg_replace('/(\[cut(.*?)?\])/','${1}[ADS]',$text);
//	$text = preg_replace('/(a name="cut"(.*?)?)/','${1}[ADS]',$text);
	$text = preg_replace('/(<a name="cut"><\/a>(.*?)?)/','${1}[ADS]',$text);
	$text = preg_replace('/(<a id="cut"><\/a>(.*?)?)/','${1}[ADS]',$text);


	return $text;
}
# функция вывода контента
function adscut_content_last($text = '')
{
	$options = mso_get_option('adscut', 'plugins', array() ); // получаем опции
	$ushko = $options['ushko'];
	if (function_exists('ushka')) {$ushkoresult = ushka("$ushko");}

	 
	$text = str_replace("[ADS]","$ushkoresult<br>",$text);
//	$text = preg_replace('/(<a name="cut"><\/a>(.*?)?)/',"$ushkoresult<br>",$text);
	return $text;
}?>