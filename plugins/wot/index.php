<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function wot_autoload($args = array())
{
	mso_hook_add( 'head', 'wot_head');
}

function wot_uninstall($args = array())
{	
	mso_delete_option('plugin_wot', 'plugins');
	return $args;
}

function wot_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_wot', 'plugins',
		array(

			'wot_rating_options' => array(
							'type' => 'textarea',
							'name' => 'Опции условия вывода wot',
							'description' => 'Примеры использования здесь <a href="http://www.mywot.com/ru/blog/adding-ratings-to-your-website?page=1">http://www.mywot.com/ru/blog/adding-ratings-to-your-website?page=1</a>',
							'default' => ''
						),

			),
		'Настройки плагина wot', // титул
		'Укажите необходимые опции.'   // инфо
	);
}



function wot_head($args = array()) 
{
	$url = getinfo('plugins_url') . 'wot/';
	$options = mso_get_option('plugin_wot', 'plugins', array());

	if ( !isset($options['wot_rating_options']) ) $options['wot_rating_options'] = '';
	if ($options['wot_rating_options'])
	{
	  echo '
    <script type="text/javascript">
    var wot_rating_options = {' . 
    $options['wot_rating_options'] . 
    '};
    </script>
    <script type="text/javascript"
    src="http://api.mywot.com/widgets/ratings.js"></script>';
	}
	else
	{
	  echo '
     <script type="text/javascript"
     src="http://api.mywot.com/widgets/ratings.js"></script>';
	}	
}



?>