<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function custom_meta_autoload($args = array())
{
	mso_hook_add( 'admin_page_form_add_all_meta', 'custom_meta_add_form');
}

# функции плагина


# callback функция 
function custom_meta_add_form()
{	
	# получение всех мета из ini-файла 
	# результат в  $all_meta
	# мета-поля, которые следует здесь отобразить описываются в ini-файле.

	$all_meta = '';
	
  // получим номер этой страницы
  $page_id = mso_segment(3);	
	
	
 	require_once( getinfo('common_dir') . 'inifile.php' ); // функции для работы с ini-файлом
	
	// получим все данные из ini-файла
	global $MSO;
	$ini_file = getinfo('plugins_dir') . 'custom_meta/meta.ini';
	
	// подключаем meta.ini из плагина 

	
	if (file_exists($ini_file)) 
	{
		$all = mso_get_ini_file( $ini_file );
	
    $CI = & get_instance();
		$CI->db->select('meta_value, meta_key');
		$CI->db->where( array ('meta_id_obj' => $page_id , 'meta_table' => 'page' ) );
		$query = $CI->db->get('meta');
		
		$page_all_meta = array();
		foreach ($query->result_array() as $row)
		{
			$page_all_meta[$row['meta_key']][] = $row['meta_value'];
		}


	// проходимся по всем ini-опциям
	// для совместимости используем вместо meta_  options_
	foreach ($all as $key=>$row)
	{
		if ( isset($row['options_key']) ) $options_key = stripslashes(trim($row['options_key']));
			else continue;
		
		if ($options_key == 'tags') continue; // метки отдельно идут
		
		if ( !isset($row['type']) ) $type = 'textfield';
			else $type = stripslashes(trim($row['type']));
		
		if ( !isset($row['values']) ) $value = '';
			else $values = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['values']))));
			
		if ( !isset($row['description']) ) $description = '';
			else $description = _mso_ini_check_php(stripslashes( trim( t($row['description']))));
			
		if ( !isset($row['delimer']) ) $delimer = '<br>';
			else $delimer = stripslashes($row['delimer']);	
			
		if ( !isset($row['default']) ) $default = '';
			else $default = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['default']))));
		
		$options_present = true; // признак, что опция есть в базе
		
		// получаем текущее значение 
		
		if (isset($page_all_meta[$options_key])) // есть в мета
		{
			foreach ($page_all_meta[$options_key] as $val)
			{
				$value = htmlspecialchars($val);
			}
		}
		else 
		{
			$options_present = false;
			$value = $default; // нет значание, поэтому берем дефолт
		}
		
    $f = '';		
		$name_f = 'f_options[' . $options_key . ']'; // название поля 
		
		$file = getinfo('plugins_dir') . 'custom_meta/meta_types/' . $type . '.php';
    if ( file_exists($file) ) require $file;
		
		if ($f) // если получили что-то в подключенном файле
		{
		  $f = NR . $f;
		  if ($description) $f .= '<p>' .  $description . '</p>';
		  $key = '<h3>' . $key . '</h3>';
		  $all_meta .= '<div>' . $key . NR . $f . '</div>';
		}  
	}
	}
	
	//if ($all_meta) $all_meta = '<h3>' . t('Пользовательские поля', 'admin') . '</h3>' . $all_meta;
	
	return $all_meta;
}

?>