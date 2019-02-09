<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Pagination_more» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-pagination-more
 */

# читаем опции плагина
$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array() ); // получаем опции
if( count($options) == 0 )
{
	echo false;
	die();
}
$def_types = array('home', 'category', 'tag');
if( isset($options['types']) && $options['types'] != '' )
{
	$types = array_map('trim', explode(',', trim($options['types'])));
	$options['types'] = array();
	foreach( $types as $type )
	{
		if( in_array($type, $def_types) )
		{
			$options['types'][] = $type;
		}
	}
}
else
{
	$options['types'] = $def_types;
}

if( !isset($options['home-offset']) || !$options['home-offset'] ) $options['home-offset'] = '';

	
$CI = & get_instance();
mso_checkreferer();
global $MSO;
	
# показать мета, сменить статус публикации или удалить
if( $post = mso_check_post(array('do', 'type', 'current', 'base')))
{
	# загружаем новую порцию записей
	if( $post['do'] == 'loadmore' && is_numeric($post['current']) )
	{
		if( in_array($post['type'], $options['types']) )
		{
			$MSO->data['type'] = $post['type'];
				
			require_once(getinfo('common_dir') . 'page.php'); # функции страниц 
			require_once(getinfo('common_dir') . 'category.php'); # функции рубрик
			require_once(getinfo('shared_dir') . 'stock/page-out/page-out.php'); # библиотека для вывода записей в цикле и вывод колонок
			require_once(getinfo('shared_dir') . 'stock/thumb/thumb.php'); # библиотека для работы с изображениями
			
			$post['base'] = str_replace('/next/1', '', $post['base']);
			#pr($post);
			
			$full_posts = mso_get_option($post['type'].'_full_text', 'templates', true); # полные или короткие записи
			$_SERVER['REQUEST_URI'] = '/'.$post['base'].'/next/'.($post['current'] + 1);
			#pr($_SERVER['REQUEST_URI']);
			if( $post['type'] == 'home' && $options['home-offset'] )
			{
				$pages = mso_get_pages(
					array( 
						'limit' => $options['home-offset'],
						#'cut' => $cut,
						#'xcut' => false,
						'show_cut' => false,
						'show_xcut' => false,
						'get_page_meta_tags' => false,
						'get_page_categories' => false,
						'get_page_count_comments' => false,
						'pagination' => false,
						),
					$pagination
				);
				#pr($pages);
				$ex_pages = array();
				foreach( $pages as $pg )
				{
					$ex_pages[] = $pg['page_id'];
				}
				#pr($ex_pages);
			}
			
			# параметры для получения страниц
			$par = array( 
					'limit' => mso_get_option('limit_post', 'templates', '7'),
					'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
					'cat_order' => 'category_id_parent',
					'cat_order_asc' => 'asc',
					'type' => false,
					'custom_type' => $post['type'],
					'content' => $full_posts,
					'slug' => str_replace($post['type'].'/', '', $post['base']),
				);
				
			if( isset($ex_pages) && $ex_pages )
			{
				$par['exclude_page_id'] = $ex_pages;
				mso_set_val('exclude_page_id', $ex_pages);
			}
				
			$MSO->data[basename(dirname(__FILE__))]['par'] = $par;

			# подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
			if ($f = mso_page_foreach('category-mso-get-pages')) require($f); 
				
			mso_hook_add('mso_get_pages', basename(dirname(__FILE__)).'_get_pages', 10);
				
			$pages = mso_get_pages($par, $pagination);	# pr($pages);

			if( $pages ) // есть страницы
			{

				if ($full_posts) // полные записи
				{
					if ($fn = mso_find_ts_file('type/'.$post['type'].'/units/'.$post['type'].'-full.php')) require($fn);
				}
				else // вывод в виде списка
				{
					if ($fn = mso_find_ts_file('type/'.$post['type'].'/units/'.$post['type'].'-list.php')) require($fn);
				}
			}
			
			die();
		}
	}
}
	
die();
	
function pagination_more_get_pages( $args = array() )
{
	global $MSO;
		
	#$args = array_replace( $args, $MSO->data[basename(dirname(__FILE__))]['par'] ); 
	#pr($args);
		
	return $args;
}	
	
?>