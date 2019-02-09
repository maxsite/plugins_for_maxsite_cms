<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function block_autoload()
{
    mso_hook_add('head', 'block_head');
}

# функция выполняется при активации (вкл) плагина
function block_activate($args = array())
{	
	mso_create_allow('block_edit', t('Админ-доступ к настройкам block'));
	return $args;
}

# Функция получения данных
function block_mso_options() 
{
	if ( !mso_check_allow('block_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_block', 'plugins', 
		array(
		    'option1' => array(
		                    'type' => 'info', 
		                    'title' => t('Оформление'),
		                    'text' => 'Оформление блока', 
	                    ),
			'option2' => array(
							'type' => 'radio', 
							'name' => t('Стиль'), 
							'description' => t('Выберите стиль наиболее подходящий'), 
							'values' => t('grey.css #green.css #blue.css'),
							'default' => 'grey.css',
							'delimer' => '    '
						),
			'option3' => array(
							'type' => 'text', 
							'name' => t('Ширина'), 
							'description' => t('Укажите ширину блока в пикселях'), 
							'default' => '500'
						),
			'option4' => array(
		                    'type' => 'textarea',
		                    'rows' => 10, 
		                    'name' => t('Код формы подписки, например, smartrisponder'), 
		                    'description' => t('Введите сюда код вашей формы подписки с сервиса.'), 
		                    'default' => ''
	                    ),
						
						
			),
		t('Настройки Блока подписки'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
	
}

function block_head($args = array()) 
{

   echo NR . '<script type="text/javascript" src="' . getinfo('plugins_url') . 'block/js/placeholder.js"></script>' . NR;
	return $args;
}
function block_show() 
{
	
 $options = mso_get_option('plugin_block', 'plugins', array());
 
 if (isset($options['option2']) and $options['option2']);
 if (isset($options['option3']) and $options['option3']);
 if (isset($options['option4']) and $options['option4']);
{
		 echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'block/css/' .$options['option2']. '" type="text/css" media="screen">' . NR;
		
	echo '<div class="conteiner" style=" width:'. $options['option3'] .'px; height:280px; ">';
	   echo '<div class="box">';
	     echo '<div class="formas">';
echo ' <form>';
echo '<div>'.$options['option4'].'</div>';
echo '</form>';
	       echo '</div>';
	   echo '</div>';
    echo '</div>';
//echo '</div>';
//	   echo '</div>';
//    echo '</div>';
	}  
	}

# функция выполняется при деактивации (выкл) плагина
function block_deactivate($args = array())
{	
	// mso_delete_option('plugin_block', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function block_uninstall($args = array())
{	
	mso_delete_option('plugin_block', 'plugins' ); // удалим созданные опции
	mso_remove_allow('block_edit'); // удалим созданные разрешения
	return $args;
}


# end file