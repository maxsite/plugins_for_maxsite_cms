<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#################
# это файл опций
#################


# доступны переменные
# 	$module_id - номер модуля
# 	$module_name - имя модуля

# ключ, тип, ключи массива
mso_admin_plugin_options('test_' . $module_id, 'modules', 
	array(
		'title' => array(
						'type' => 'text', 
						'name' => 'Название', 
						'description' => 'Описание', 
						'default' => 'нет'
					),
		),
	$module_name . ' ('. $module_id . ')', // титул
	'Укажите необходимые опции модуля.' // инфо
);

?>