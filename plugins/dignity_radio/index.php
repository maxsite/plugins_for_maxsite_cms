<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 */

function dignity_radio_autoload()
{
	mso_hook_add( 'admin_init', 'dignity_radio_admin_init');
	
	// регестируем виджет
	mso_register_widget('dignity_radio_widget', t('Радио онлайн', __FILE__));
}

function dignity_radio_activate($args = array())
{	
	mso_create_allow('dignity_radio_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Онлайн радио»', __FILE__));

	return $args;
}

function dignity_radio_uninstall($args = array())
{

	mso_delete_option('plugin_dignity_radio', 'plugins');
	mso_remove_allow('dignity_radio_edit');
	
	// удаляем настройки виджета
	mso_delete_option_mask('dignity_radio_widget_', 'plugins');

	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_radio_widget($num = 1) 
{
	$widget = 'dignity_radio_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_radio_widget_custom($options, $num);
}

# функции плагина
function dignity_radio_widget_custom($options = array(), $num = 1)
{
	
	$out = '';
	
	$options = mso_get_option('plugin_dignity_radio', 'plugins', array());
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>')
		. $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		
	$out .= '<p>' . $options['textdo'] . '</p>';
	
	$plugin_url = getinfo('plugins_url') . 'dignity_radio/';
		
	$out .= '<embed src="' . $plugin_url . 'mju.swf" flashvars="playlist=' . $plugin_url . 'playlist.mpl&auto_run=true&repeat_one=false&shuffle=false"
		loop="false" menu="false" quality="high" wmode="transparent" bgcolor="#ffffff" width="200" height="90" name="mju"
		allowScriptAccess="sameDomain" swLiveConnect="true" type="application/x-shockwave-flash"
		pluginspage="http://www.macromedia.com/go/getflashplayer" />';
	
	$out .= '<p>' . $options['textposle'] . '</p>';
	
	return $out;	
}

function dignity_radio_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_radio_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_radio'; 
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Онлайн радио', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_radio_admin_page');
	
	return $args;
}

function dignity_radio_admin_page($args = array()) 
{

	if ( !mso_check_allow('dignity_radio_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Онлайн радио', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Онлайн радио', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'dignity_radio/admin.php');
}

#end of file
