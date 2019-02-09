<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
mso_cur_dir_lang('admin');



function buildtable($datatable = array())
/* построение таблицы */
	{$table = FALSE;
	if (is_array($datatable))
		{$CI = & get_instance(); 
		$CI->load->library('table'); 
		$tmpl = array ('table_open' => '<table class="grshtable rowstyle-alt" border="1" cellpadding="4" cellspacing="0">',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="sortable" >',
			'heading_cell_end'    => '</th>',	

			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',

			'row_alt_start'       => '<tr class="alt">',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
			);
		$CI->table->set_template($tmpl); 
		$table=$CI->table->generate($datatable); 
		$CI->table->clear();
		}
	return $table;
	};
	
function deletefff($arg)
	{
		if (!file_exists($arg)) return true;
		chmod($arg, 0777);
		$d=opendir($arg);
		while($f=readdir($d))
			{
				if($f!="."&&$f!="..")
					{
						if(is_dir($arg."/".$f))
							deletefff($arg."/".$f);
						else 
							unlink($arg."/".$f);
					}
			}
		closedir($d);							
		rmdir($arg);
	}
	

function delete_pages($args = array())
	{	
		global $MSO;
		global $grgll;
		foreach ($args as $delp)
			{
				$new_dir = getinfo('uploads_dir').$grgll['uploads_pict_dir'].'/'.$grgll['prefix'];
				$page_id = (int) $delp;
				if (!is_numeric($page_id)) $page_id = false; // не число
				else $page_id = (int) $page_id;
				
				if (!$page_id) // ошибка! 
					{
						echo '<div class="error">' . t('Ошибка удаления', 'admin') . '</div>';
					}
				else 
					{
						$data = array(
						'user_login' => $MSO->data['session']['users_login'],
						'password' => $MSO->data['session']['users_password'],
						'page_id' => $page_id,
									);
					}
					
				echo $dir_page_id = $new_dir.$page_id;
				require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
				$result = mso_delete_page($data);
				deletefff ($dir_page_id);
				
				if (isset($result['result']) and $result['result'])
					{
						if ( $result['result'] ) 
							{					
								echo '<div class="update">' . t('Страница удалена', 'admin') . '</div>';
							}
						else
							{
								echo '<div class="error">' . t('Ошибка при удалении', 'admin') . ' ('. $result['description'] . ')</div>';
							}
					}
				else
					{
						echo '<div class="error">' . t('Ошибка при удалении', 'admin') . ' ('. $result['description'] . ')</div>';
					}			
			}
	}	
	
function sizetable($datatable = array())
/* ВСПОМОГАТЕЛЬНАЯ принимает массив, возвращает данные о его размере 
в виде массива с тремя значениями*/
	{
	$size['row']=FALSE;	
	$size['col']=FALSE;
	$size['full']=FALSE;
	if ($datatable != FALSE)
		{
		$size['row']=count($datatable);	
		if (isset($datatable[1])) {$size['col']=count($datatable[1]);}
		elseif (isset($datatable[0])) {$size['col']=count($datatable[0]);}
		else {$size['col']=1;};
		$size['full']=$size['row']*$size['col'];
		}
	return $size;
	};

function get_title_field_db($nmfdb)
/* принимает внутренние имена, возвращает человеческие названия 
которые берет из файла config.php*/
	{
	global $MSO;
	global $nmfpdb;
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	$out = $nmfdb;
	if (isset($nmfpdb[$out])) $out = $nmfpdb[$out];
	return $out;
	}


/* ф-ция возвращает массив групп тегов */	
function get_group_tag ($arr = array())
	{
	global $grgll;
	if ( !isset($arr['cache']) ) $arr['cache'] = FALSE;	// по умолчанию не кешируется, если явно указываем, то берем из кэша
	if ( !isset($arr['inverse']) ) $arr['inverse'] = FALSE;	// инверсировать arr, т.е. ключи - теги, значения - группы включающие эти теги
	// кэш	
	$cache_key = $grgll['main_key_options'].'_group_tag' . serialize($arr);
	if ($arr['cache'] != FALSE)
		{
			$k = mso_get_cache($cache_key);
			if ($k) return $k; // да есть в кэше
		}
			
	$CI = & get_instance();
	$group_tags = array();
	$CI->db->select('meta_value, meta_table, meta_menu_order, meta_id_obj, meta_key, meta_id');
	$CI->db->where('meta_key', 'group_tag');
	$CI->db->group_by('meta_value');
	$query = $CI->db->get('meta');
	if ($query->num_rows() > 0)
		{
			$group_tags = array();
			foreach ($query->result_array() as $row)
				{
					if ($row['meta_key'] == 'group_tag') $group_tags = unserialize($row['meta_value']);
				}			
		}
	
	if ($arr['inverse'] != FALSE) 
		{
		$tags_group = array();
		foreach ($group_tags as $key => $val)
			{	
			if (is_array($val))
				{
				foreach ($val as $key1 => $val1)
					{	
					$tags_group[$val1] = $key;
					}
				}
			}
		mso_add_cache($cache_key, $group_tags); // сразу в кэш добавим
		//pr ($tags_group);
		return $tags_group;
		}
	else
		{
		foreach ($group_tags as $key => $val)
			{	
			if (is_array($val))
				{
				foreach ($val as $key1 => $val1)
					{	
					$group_tags[$key][$val1] = $val1;
					if ($key1 != $val1) unset ($group_tags[$key][$key1]);
					}
				}
			}
		mso_add_cache($cache_key, $group_tags); // сразу в кэш добавим
		//pr ($group_tags);
		return $group_tags;
		}
	}
	
/* ф-ция возвращает массив с ключами - именами тегов, значения - массивы с номерами страниц,
для которых активны эти теги */
function get_pages_tag ($arr = array())
	{
	
	global $grgll;
	if ( !isset($arr['cache']) ) $arr['cache'] = FALSE;	// по умолчанию не кешируется, если явно указываем, то берем из кэша
	
	// кэш
	$cache_key = $grgll['main_key_options'].'_pages_tag' . serialize($arr);
	if ($arr['cache'] != FALSE)
		{
			$k = mso_get_cache($cache_key);
			if ($k) return $k; // да есть в кэше
		}	
	
	$CI = & get_instance();
	$tag_pages = array();
	$CI->db->select('meta_value, meta_table, meta_id_obj, meta_key');
	$CI->db->where('meta_key', 'tags');
	$query = $CI->db->get('meta');
	if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
				{
					$tag_pages[$row['meta_value']][$row['meta_id_obj']] = $row['meta_id_obj'];
				}	
		}
	mso_add_cache($cache_key, $tag_pages); // сразу в кэш добавим
	return $tag_pages;	
	}
?>