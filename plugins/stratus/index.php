<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function stratus_autoload()
{
	mso_hook_add( 'head', 'stratus_head');
}

# функция выполняется при активации (вкл) плагина
function stratus_activate($args = array())
{	
	mso_create_allow('stratus_edit', t('Админ-доступ к настройкам stratus'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function stratus_deactivate($args = array())
{	
	mso_delete_option('plugin_stratus', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function stratus_uninstall($args = array())
{	
	mso_delete_option('plugin_stratus', 'plugins' ); // удалим созданные опции
	mso_remove_allow('stratus_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function stratus_mso_options() 
{
	if ( !mso_check_allow('stratus_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_stratus', 'plugins', 
		array(
			'align' => array(
							'type' => 'select', 
							'name' => t('Укажите расположение плеера'), 
							'description' => t('Расположение плеера: "bottom" (внизу), "top" (сверху)'),
							'values' => t('#1||top #2||bottom'),
							'default' => 'top'
						),
			'artist' => array(
							'type' => 'text', 
							'name' => t('Укажите ссылку на трек'), 
							'description' => t('Например: http://soundcloud.com/foofighters/sets/wasting-light'),
							'default' => 'https://soundcloud.com/retroid/digital-department-outburst'
						),
			),
		t('Настройки плагина stratus'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

function stratus_head($arg = array())
{
	$options_key = 'plugin_stratus';
	$options = mso_get_option($options_key, 'plugins', array()); 
	if ( !isset($options['align']) ) $options['align'] = 'top'; // разрешено ли логирование?
	if ( !isset($options['artist']) ) $options['artist'] = 'https://soundcloud.com/retroid/digital-department-outburst';
	
	echo '<script type="text/javascript" src="http://stratus.sc/stratus.js"></script>';
	echo '<script type="text/javascript">
  	$(document).ready(function(){
  	  $.stratus({
  	   align: "' . $options['align'] . '",
   	   links: "' . $options['artist'] . '"
  	  });
 	});
	</script>';

	return $arg;
}

# end file
