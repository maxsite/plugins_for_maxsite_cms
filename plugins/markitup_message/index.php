<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Font Awesome Plugin for markItUp
 * (c) http://olorg.ru/
 */

#markitup_mesage


# функция автоподключения плагина
function markitup_message_autoload()
{
	mso_hook_add('editor_markitup_bbcode', 'markitup_message_bb');
	mso_hook_add('head', 'load_message');
	mso_hook_add('admin_head', 'load_message');
	
}
/*
# функция выполняется при активации (вкл) плагина
function markitup_font_awesome_activate($args = array())
{	
	mso_create_allow('markitup_font_awesome_edit', t('Админ-доступ к настройкам markitup_font_awesome'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function markitup_font_awesome_deactivate($args = array())
{	
	// mso_delete_option('plugin_markitup_font_awesome', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function markitup_font_awesome_uninstall($args = array())
{	
	mso_delete_option('plugin_markitup_font_awesome', 'plugins' ); // удалим созданные опции
	mso_remove_allow('markitup_font_awesome_edit'); // удалим созданные разрешения
	return $args;
}
*/
# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
/*
function markitup_font_awesome_mso_options() 
{
	if ( !mso_check_allow('markitup_font_awesome_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_markitup_font_awesome', 'plugins', 
		array(
			'option1' => array(
							'type' => 'text', 
							'name' => t('Название'), 
							'description' => t('Описание'), 
							'default' => ''
						),
			),
		t('Настройки плагина markitup_font_awesome'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}
*/
# функции плагина
function markitup_message_bb ($args = array())
{
echo <<<EOF
{separator:'---------------' },	

{name:'Message', openWith:'[div(message [![Css message]!])]', closeWith:'[/div]', className:"page-red", dropMenu :[  
			{name:'Note', openBlockWith:'[div(message note)]', closeBlockWith:'[/div]', className:"message small note"}, 
			{name:'Alert', openBlockWith:'[div(message alert)]', closeBlockWith:'[/div]', className:"message small alert"}, 
			{name:'Idea', openBlockWith:'[div(message idea)]', closeBlockWith:'[/div]', className:"message small idea"}, 
			{name:'Error', openBlockWith:'[div(message error)]', closeBlockWith:'[/div]', className:"message small error"}, 
			{name:'Ok', openBlockWith:'[div(message ok)]', closeBlockWith:'[/div]', className:"message small ok"}, 
			{name:'About', openBlockWith:'[div(message about)]', closeBlockWith:'[/div]', className:"message small about"}, 
			{name:'Mail', openBlockWith:'[div(message mail)]', closeBlockWith:'[/div]', className:"message small mail"}, 
			{name:'Home', openBlockWith:'[div(message home)]', closeBlockWith:'[/div]', className:"message small home"}, 
			{name:'Question', openBlockWith:'[div(message question)]', closeBlockWith:'[/div]', className:"message small question"}, 
		]},
		
EOF;
	return $args;
}

function load_message ($args = array())
{
	echo mso_load_style( getinfo('plugins_url').'markitup_message/css/style.css' ) . NR;
	return $args;
}

/*
function markitup_font_awesome_custom($arg = array())
{

	
}
*/
# end file