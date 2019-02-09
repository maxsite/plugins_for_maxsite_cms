<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function category_editor_autoload($args = array())
{
	mso_create_allow('category_editor_edit', t('Доступ к настройкам «Category editor»', __FILE__));
	mso_hook_add( 'admin_init', 'category_editor_admin_init'); # хук на админку
	mso_hook_add('init', 'category_editor_page_init');
	
	if ( mso_segment(1) == 'category')
	{
		mso_hook_add('head_meta', 'category_editor_head_title');
	}
	return $args;
}



# функция выполняется при указаном хуке admin_init
function category_editor_admin_init($args = array())
{
	if ( !mso_check_allow('category_editor_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'category_editor'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Редактор категорий', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'category_editor_admin_page');
	return $args;
}



# функция вызываемая при хуке, указанном в mso_admin_url_hook
function category_editor_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('category_editor_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Category editor', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Category editor', __FILE__) . ' - " . $args; ' );
	
	echo '<link rel="stylesheet" href="'.getinfo('plugins_url').'category_editor/assets/css/ce_style.css">'.NR;
	echo '<link rel="stylesheet" href="'.getinfo('plugins_url').'category_editor/assets/css/ce.alert.css">'.NR;
	echo '<link rel="stylesheet" href="'.getinfo('plugins_url').'category_editor/assets/css/jquery.contextMenu.css">'.NR;
	
	
	
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/jquery-ui.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/func.js.ce.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/ce.alert.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/jquery.ce.editor.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/main.js.ce.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/onload.js.ce.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/popup.js.ce.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/touch-dnd.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/jquery.contextMenu.js"></script>'.NR;
	echo '<script src="'.getinfo('plugins_url').'category_editor/assets/js/context.js.ce.js"></script>'.NR;
	
	
	
	$a  = mso_admin_link_segment_build('/admin/category_editor', '', t('Список'), 'select', 'mx-home');
	$a .= ' '.mso_admin_link_segment_build('/admin/category_editor', 'setting', t('Настройки полей'), 'select', 'ce-setting');
	$a .= ' '.mso_admin_link_segment_build('/admin/category_editor', 'docs', t('Документация'), 'select', 'ce-docs');
	
	echo '<h1>Редактор категорий</h2>';
	echo '<p class="info">Плагин позволяет настраивать и редактировать мета-поля для категорий</p>';
	
	if(function_exists('category_title'))
	{
		echo '<div class="error">Для нормальной работы необходимо выключить плагин «Category title»</div>';
		//return;
	}
	
	
	echo '<div class="admin-h-menu">';
	echo $a;
	echo '</div>';
	
	if(mso_segment(3) and $seg = mso_segment(3) == 'setting')
	{
		require(getinfo('plugins_dir') . 'category_editor/setting.php');
	}
	elseif(mso_segment(3) and $seg = mso_segment(3) == 'edit')
	{
		require(getinfo('plugins_dir') . 'category_editor/edit.php');
	}
	elseif(mso_segment(3) and $seg = mso_segment(3) == '_install')
	{
		require(getinfo('plugins_dir') . 'category_editor/_install.php');
	}
	elseif(mso_segment(3) and $seg = mso_segment(3) == 'docs')
	{
		require(getinfo('plugins_dir') . 'category_editor/docs/doc_start.php');
	}
	else
	{
		require(getinfo('plugins_dir') . 'category_editor/admin.php');
	}
}


# функция выполняется при деинсталяции плагина
function category_editor_uninstall($args = array())
{
	$CI = &get_instance();
	$CI->db->like('meta_key', '_ce_', 'after');
	$CI->db->where('meta_table', 'meta_table');
	$CI->db->delete('meta');
	mso_delete_option('category_editor_fields', 'plugins');
	mso_delete_option('_ce_virtual_menu_items', 'plugins');
	
	$CI->load->dbforge();
	$tab_prefix = $CI->db->dbprefix;
	
	$query = "ALTER TABLE `{$tab_prefix}cat2obj` DROP `c2obj_order`, DROP `c2obj_status`";
	$CI->db->query($query);
	
	$query = "DROP TABLE `{$tab_prefix}ce_menu`";
	$CI->db->query($query);
	return $args;
}


function category_editor_page_init()
{
	require_once(getinfo('plugins_dir').'category_editor/class_cat_editor.php');
	require_once(getinfo('plugins_dir').'category_editor/custom/ce_admin_function.php');
	if (mso_segment(1) == 'category')
	{
		CategoryEditor::getInstance()->load_from_slug = mso_segment(2);
	}
}

function category_editor_head_title($args = array())
{
	if(mso_get_val('ce_set_new_title'))
	{
		$meta['ct_title'] = mso_get_val('ce_set_new_title');
		# если меняем title, то надо поменять canonical
		mso_hook_add('canonical', 'ce_change_canonical');
	}
	else
	{
		$meta['ct_title'] = CategoryEditor::getInstance()->title;
	}
	$meta['ct_keywords'] = CategoryEditor::getInstance()->keywords;
	$meta['ct_description'] = CategoryEditor::getInstance()->description;
	
	$meta['ct_description'] = str_replace("_NR_", "\n", $meta['ct_description']);
	
	if (!$meta['ct_title'])       $meta['ct_title']       = ce_head_meta('title',       $args['args'], $args['format'], $args['sep']);
	if (!$meta['ct_keywords'])    $meta['ct_keywords']    = ce_head_meta('keywords',    $args['args'], $args['format'], $args['sep']);
	if (!$meta['ct_description']) $meta['ct_description'] = ce_head_meta('description', $args['args'], $args['format'], $args['sep']);

	if ($args['info'] == 'title')
	{
		if (mso_current_paged() > 1)
		{
			if ($meta['ct_title']) $meta['ct_title'] .= ' - Страница ' . mso_current_paged();
				else $meta['ct_title'] = 'Страница ' . mso_current_paged();
		}
		return $meta['ct_title'];
	}

	if ($args['info'] == 'keywords')    return $meta['ct_keywords'];
	if ($args['info'] == 'description') return $meta['ct_description'];
	return '';
}

function ce_head_meta($info = 'title', $args = '', $format = '%page_title%', $sep = '', $only_meta = false )
{
	// ошибочный info
	if ( $info != 'title' and $info != 'description' and $info != 'keywords') return '';

	global $MSO;

	// измененный для вывода титле хранится в $MSO->title description или keywords

	if (!$args) // нет аргумента - выводим что есть
	{
		if ( !$MSO->$info )	$out = $MSO->$info = getinfo($info);
		else $out = $MSO->$info;
	}
	else // есть аргументы
	{
		if (is_scalar($args)) $out = $args; // какая-то явная строка - отдаем её как есть
		else // входной массив - скорее всего это страница
		{
			// %page_title% %title% %category_name%
			// | это разделитель, который = $sep
			// pr($args);

			$category_name = '';
			$category_desc = '';
			$page_title = '';
			$users_nik = '';
			$title = getinfo($info);

			// if ( !$info ) $format = '%title%';

			// название рубрики
			if ( isset($args[0]['category_name']) ) 
			{
				$category_name = htmlspecialchars($args[0]['category_name']);
				
				// по названию рубрики ищем её описание в $args[0]['page_categories_detail'][$id]['category_desc']
				if (isset($args[0]['page_categories_detail']))
				{
					foreach ($args[0]['page_categories_detail'] as $id => $val)
					{
						if ($args[0]['category_name'] === $val['category_name'] )
						{
							$category_desc = htmlspecialchars($val['category_desc']);
							break;
						}
					}
				}
				
				if (!$category_desc) $category_desc = $category_name; // если нет описания, то берем название
			}
			
			if ( isset($args[0]['page_title']) ) $page_title = $args[0]['page_title'];
			if ( isset($args[0]['users_nik']) ) $users_nik = $args[0]['users_nik'];

			// если есть мета, то берем её
			if ( isset($args[0]['page_meta'][$info][0]) and $args[0]['page_meta'][$info][0] )
			{
				if ( $only_meta ) $category_name = $category_desc = $title = $sep = '';
				$page_title = $args[0]['page_meta'][$info][0];

				if ( $info!='title') $title = $page_title;
			}

			$arr_key = array( '%title%', '%page_title%',  '%category_name%', '%category_desc%', '%users_nik%', '|' );
			$arr_val = array( htmlspecialchars($title),  htmlspecialchars($page_title), htmlspecialchars($category_name), $category_desc, htmlspecialchars($users_nik), $sep );
			//$arr_val = array( $title ,  $page_title, $category_name, $category_desc, $users_nik, $sep );
			
			$out = str_replace($arr_key, $arr_val, $format);
		}
	}

	// отдаем результат, сразу же указывая измененный $info в $MSO
	$out = $MSO->$info = trim($out);

	return $out;
}

function ce_change_canonical($args = array())
{
	//global $MSO;
	//pr(mso_current_url());
	$segments = mso_current_url(false, true);
	
	$url = getinfo('site_url');
	foreach($segments as $val)
	{
		if($val == 'next')
		{
			break;
		}
		$url .= $val . '/';
	}
	$url = rtrim($url, '/');
	//pr($url);
	return $url;
}
