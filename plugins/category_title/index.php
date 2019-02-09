<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function category_title_autoload($args = array())
{
	mso_create_allow('category_title_edit', t('Доступ к настройкам «Category title»', __FILE__));
	mso_hook_add( 'admin_init', 'category_title_admin_init'); # хук на админку
	if ( mso_segment(1) == 'category')
	{
		mso_hook_add( 'head_meta', 'category_title' );
		mso_hook_add('init', 'category_title_page_init');
	}
	return $args;
}



# функция выполняется при указаном хуке admin_init
function category_title_admin_init($args = array())
{
	if ( !mso_check_allow('category_title_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'category_title'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Category title', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'category_title_admin_page');

	return $args;
}



# функция вызываемая при хуке, указанном в mso_admin_url_hook
function category_title_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('category_title_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Category title', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Category title', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'category_title/admin.php');
}



function my_mso_head_meta($info = 'title', $args = '', $format = '%page_title%', $sep = '', $only_meta = false )
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



# функция выполняется при деинсталяции плагина
function category_title_uninstall($args = array())
{
	$CI = &get_instance();
	$CI->db->delete('meta', array('meta_key' => 'category_title'));
	return $args;
}

function category_title($args = array())
{
	$meta['ct_title'] = CategoryTitle::getInstance()->title;
	$meta['ct_keywords'] = CategoryTitle::getInstance()->keywords;
	$meta['ct_description'] = CategoryTitle::getInstance()->description;
	
	// шаблон категории
	mso_set_val('category_temlate', CategoryTitle::getInstance()->template);

	$meta['ct_description'] = str_replace("_NR_", "\n", $meta['ct_description']);

	if (!$meta['ct_title'])       $meta['ct_title']       = my_mso_head_meta('title',       $args['args'], $args['format'], $args['sep']);
	if (!$meta['ct_keywords'])    $meta['ct_keywords']    = my_mso_head_meta('keywords',    $args['args'], $args['format'], $args['sep']);
	if (!$meta['ct_description']) $meta['ct_description'] = my_mso_head_meta('description', $args['args'], $args['format'], $args['sep']);

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

function category_title_page_init()
{
	require_once(getinfo('plugins_dir').'category_title/class_cat.php');
}

