<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function googlebutton_autoload()
{
	if (!is_feed() and (is_type('page')))
	{
		mso_hook_add('content_content', 'googlebutton_content'); # хук на вывод контента
		mso_hook_add('head', 'googlebutton_head');
	}
}


# функция выполняется при деинсталяции плагина
function googlebutton_uninstall($args = array())
{	
	mso_delete_option('plugin_googlebutton', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function googlebutton_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_googlebutton', 'plugins', 
		array(
			'vk_on' => array(
						'type' => 'select', 
						'name' => 'Включить ВКонтакте?',
						'description' => 'Отображать кнопку Вконтакте +1', 
						'values' => 'yes||да # no||нет',
						'default' => 'yes'
					),										
			'fa_on' => array(
						'type' => 'select', 
						'name' => 'Включить Facebook?',
						'description' => 'Отображать кнопку Facebook +1', 
						'values' => 'yes||да # no||нет',
						'default' => 'yes'
					),										
			'fa_cs' => array(
						'type' => 'select', 
						'name' => 'Вид facebook?',
						'description' => 'Светлая или темная тема', 
						'values' => 'light||светлая # dark||темная',
						'default' => 'light'
					),
			'go_on' => array(
						'type' => 'select', 
						'name' => 'Включить Google?',
						'description' => 'Отображать кнопку Google +1', 
						'values' => 'yes||да # no||нет',
						'default' => 'yes'
					),										
			'tw_on' => array(
						'type' => 'select', 
						'name' => 'Включить Tweetmeme?',
						'description' => 'Отображать кнопку Retweet', 
						'values' => 'yes||да # no||нет',
						'default' => 'yes'
					),										
			'vk_id' => array(
						'type' => 'text', 
						'name' => 'ВКонтакте apiId', 
						'description' => 'Можно получить по адресу http://vkontakte.ru/developers.php?o=-1&p=Like.',
						'default' => '2396003'
					),										
			),
		'Настройки плагина GoogleButton', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

function googlebutton_head($arg = array())
{
	$options = mso_get_option('plugin_googlebutton', 'plugins', array() ); // получаем опции
	
	if (!isset($options['vk_on'])) $options['vk_on'] = 'yes'; 
	if (!isset($options['go_on'])) $options['go_on'] = 'yes'; 
	if ($options['vk_on'] == 'yes') 
	{
		echo '<script src="http://userapi.com/js/api/openapi.js?32" type="text/javascript" charset="windows-1251"></script>';
	}
	if ($options['go_on'] == 'yes') 
	{
//		$url = mso_current_url(true);
//		echo '<link rel="canonical" href="'.$url.'" />';
		echo '<script type="text/javascript" src="http://apis.google.com/js/plusone.js"> {lang: "ru"} </script>';
	}
	return $arg;
}

# функции плагина
function googlebutton_content($text = '')
{
	global $page;
	$url = mso_current_url(true);	
	if (!is_type('page') and !is_type('home')) return $text;
	
	// если запись не опубликована, не отображаем блок
	if (is_type('page') and isset($page['page_status']) and $page['page_status'] != 'publish') return $text;
	
	$options = mso_get_option('plugin_googlebutton', 'plugins', array() ); // получаем опции
	
	if (!isset($options['vk_id'])) $options['vk_id'] = 0; 
	if (!isset($options['vk_on'])) $options['vk_on'] = 'yes'; 
	if (!isset($options['fa_cs'])) $options['fa_cs'] = 'light'; 
	if (!isset($options['fa_on'])) $options['fa_on'] = 'yes'; 
	if (!isset($options['go_on'])) $options['go_on'] = 'yes'; 
	if (!isset($options['tw_on'])) $options['tw_on'] = 'yes'; 
	
	if ($options['go_on'] == 'yes') 
	{
		$text .= '<div style="float: left; width: 60px;" width="60px"><g:plusone size="tall" href="'.$url.'"></g:plusone> </div>';
	}
	if ($options['fa_on'] == 'yes') 
	{
		$text .= '<span id="fb-root"></span><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="'.$url.'" send="false" layout="box_count" width="60px" show_faces="false"'.(($options['fa_cs']=='dark')?' colorscheme="dark"':'').' font="verdana"></fb:like>';
	}
	if ($options['vk_on'] == 'yes') 
	{
		$text .= '<script type="text/javascript">VK.init({ apiId: '.$options['vk_id'].', onlyWidgets: true });</script><span id="vk_like" width="60px"></span><script type="text/javascript"> VK.Widgets.Like("vk_like", {type: "vertical"});</script>';	
	}
	if ($options['tw_on'] == 'yes') 
	{
		$text .= '<span class="tweetmeme_com" style="80px"><script type="text/javascript">tweetmeme_url = \'' . $url . '\';</script><script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script></span>';
	}
	return $text;
}


# end file