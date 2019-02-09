<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function modules_autoload()
{
	
	if (file_exists(getinfo('template_dir') . 'modules.php')) 
	{
		require(getinfo('template_dir') . 'modules.php'); // здесь определяются модули
		mso_hook_add( 'admin_init', 'modules_admin_init'); # хук на админку
	}
	
	mso_create_allow('modules_edit', t('Админ-доступ к modules', __FILE__));
}

# функция выполняется при деинсталяции плагина
function modules_uninstall($args = array())
{	
	mso_delete_float_option('module', 'module'); // удалим опции
	mso_remove_allow('modules_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function modules_admin_init($args = array()) 
{
	if ( !mso_check_allow('modules_edit') ) 
		return $args;
	
	$this_plugin_url = 'modules'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Модули', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'modules_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function modules_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('modules_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Модули', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Модули', __FILE__) . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'modules/admin.php');
}


# определяем модуль
# использовать в шаблонном modules.php
function modules_set($module_id = '1', $module_name = 'Модуль')
{
	global $MODULES;
	
	$MODULES[$module_id] = array(
			'id' => (int) $module_id, 
			'name' => $module_name,
			);
			
	# в опциях модуля храним
	//	'file_name'=>'', // подключаемый require файл
	//	'php_code'=>'', // можно произвольный php указать
}

# вывод модуля
function modules_out($module_id = '1')
{
	global $MODULES;
	
	# здесь храним опции модулей - массив по id
	static $all = false;
	
	# чтобы не елозить каждый раз опции, считываем их в static
	if (!$all) $all = mso_get_float_option('modules', 'modules', array());
	
	if (isset($MODULES[$module_id]) and isset($all[$module_id]))
	{
		// есть такой модуль

		// $module_options = mso_get_option('module_' . $module_id, 'module', array());
		
		if ($all[$module_id]['file_name'] != 'none')
		{
			// указан какой-то файл
			$fn = $all[$module_id]['file_name'];
			if (strpos($fn, 'TEMPLATE:') !== false)
			{
				// каталог шаблона
				$fn = str_replace('TEMPLATE:', '', $fn);
				$fn = getinfo('template_dir') . 'modules/' . $fn;
				
			}
			elseif (strpos($fn, 'PLUGIN:') !== false)
			{
				// каталог плагина
				$fn = str_replace('PLUGIN:', '', $fn);
				$fn = getinfo('plugins_dir') . 'modules/modules/' . $fn;
			}
			
			
			elseif (strpos($fn, 'OTHER:') !== false)
			{
				// каталог другого плагина
				$file_key = str_replace('OTHER:', '', $fn);
	      global $module_in_plugin;
      	
				$plugin_name = $module_in_plugin[$file_key]['plugin_name'];
	      $file_name = $module_in_plugin[$file_key]['file_name'];
	      $module_name = $module_in_plugin[$file_key]['module_name'];
	      
				$fn = getinfo('plugins_dir') . $plugin_name . '/modules/' . $file_name;
			}			
			
			if (file_exists($fn)) require($fn);
			
		}
		else
		{
			// нужно выполнять php-код - если он есть
			if ($all[$module_id]['php_code'])
			{
				eval( '?>' . stripslashes( $all[$module_id]['php_code'] ) . '<?php ');
			}
		}
	}
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function modules_mso_options() 
{
	global $MODULES;
	
	# для каждого модуля/файла можно задать свои опции
	# настраиваем через стандартный  mso_admin_plugin_options
	# только опции передаем в зависисмоти от сегмента
	//сайт/admin/plugin_options/modules/id/PLUGIN:test.php
	if ($module_id = mso_segment(4) and $fn = mso_segment(5))
	{
		if (isset($MODULES[$module_id])) // есть такой модуль
		{
			// смотрим расположение опций - это указанный файл, только в подкаталоге modules/options
			
			if (strpos($fn, 'TEMPLATE:') !== false)
			{
				// каталог шаблона
				$fn = str_replace('TEMPLATE:', '', $fn);
				$fn = getinfo('template_dir') . 'modules/options/' . $fn;
				
			}
			elseif (strpos($fn, 'PLUGIN:') !== false)
			{
				// каталог плагина
				$fn = str_replace('PLUGIN:', '', $fn);
				$fn = getinfo('plugins_dir') . 'modules/modules/options/' . $fn;
			}
			
			elseif (strpos($fn, 'OTHER:') !== false)
			{
				// каталог другого плагина
				global $module_in_plugin;
				
				$file_key = str_replace('OTHER:', '', $fn);
								
				$plugin_name = $module_in_plugin[$file_key]['plugin_name'];
	      $file_name = $module_in_plugin[$file_key]['file_name'];
	      $module_name = $module_in_plugin[$file_key]['module_name'];
	      
				$fn = getinfo('plugins_dir') . $plugin_name . '/modules/options/' . $file_name;		

			}			
			
			
			$module_name = $MODULES[$module_id]['name'];
			echo $fn . ' ' . $module_name;
			if (file_exists($fn)) require($fn);
			else
			{
				# ошибочный файл
				mso_admin_plugin_options('modules', 'modules', 
					array(),
					'Ошибочный файл опций модуля', // титул
					'Отстствует файл опций.' // инфо
				);
			}
		}
		else
		{
			# ошибочный id модуля
			mso_admin_plugin_options('modules', 'modules', 
				array(),
				'Ошибочный номер модуля', // титул
				'Настройки модулей можно выполнить со страницы плагина.' // инфо
			);
		}
	}
	else
	{
		// номер не указан - общие настройки плагина - не отображаются
		mso_admin_plugin_options('modules', 'modules', 
			array(),
			'Модули', // титул
			'Настройки модулей можно выполнить со страницы плагина.' // инфо
		);
		
	}
}

?>