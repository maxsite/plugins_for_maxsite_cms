<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
	require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
	
	

?>
<h1><?= t('Социальный счётчик') ?></h1>
<p class="info"><?= t('Список всех записей с данными статистики') ?></p>

<?php

	$CI->load->library('table');
	$CI->load->helper('form');
	
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', t('Заголовок'), 'G+', 'ВК', 'twi', 'fb');
	
	
	if ( !mso_check_allow('admin_page_edit_other') )
	{
		# echo 'запрещено редактировать чужие страницы';
		$current_users_id = getinfo('session');
		$current_users_id = $current_users_id['users_id'];
	}
	else $current_users_id = false;
	
	
	$par = array( 
			'limit' => 30, // колво записей на страницу
			'type' => false, // любой тип страниц
			'custom_type' => 'home', // запрос как в home
			'order' => 'page_date_publish', // запрос как в home
			'order_asc' => 'desc', // в обратном порядке
			'page_status' => false, // статус любой
			'date_now' => false, // любая дата
			//'content'=> false, // без содержания
			'page_id_autor'=> $current_users_id, // только указанного автора
			'cut' => ' ',
			);
	
	$CI->db->select('category_id, category_name');
	$CI->db->order_by('category_name');
	$CI->db->where('category_type', 'page');
	
	$query = $CI->db->get('category');

	if ($query and $query->num_rows() > 0) 
	{
		//echo '<h1>Страницы по рубрикам</h1>';
		$cat_segment_id = 0;
		
		if (mso_segment(3) == 'category') $cat_segment_id = (int) mso_segment(4);
		
		echo '<p class="admin_page_filtr"><strong>'
				. t('Рубрика')
				. ':</strong> ';
		
		
		
		require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
		
		$all_cats = mso_cat_array_single('page', 'category_id', 'ASC', ''); // все рубрики для вывода кол-ва записей
		# pr($all_cats);
		
		echo '<select class="admin_page_filtr">';
		
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		
		echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любая') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
			
			$selected = ($cat_segment_id != $nav['category_id']) ? '' : ' selected';
			
			echo '<option value="' . getinfo('site_admin_url'). 'page/category/' . $nav['category_id'] .'"' . $selected . '>' . $nav['category_name'] . ' ('. count($all_cats[$nav['category_id']]['pages']) . ')</option>';
		}

		echo '</select>';
	}

	
	
	$CI->db->select('page_type_id, page_type_name');
	$CI->db->order_by('page_type_name');
	
	$query = $CI->db->get('page_type');
	
	if ($query->num_rows() > 0) 
	{
		$type_segment_id = 0;

		if (mso_segment(3) == 'type') 
		{
			$type_segment_id = (int) mso_segment(4); 
			$type_segment_name = '';
		}
		
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
				. t('Тип')
				. ':</strong> ';
		
		echo '<select class="admin_page_filtr">';
		
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		
		echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любой') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
		
			$selected = ($type_segment_id != $nav['page_type_id']) ? '' : ' selected';
			
			echo '<option value="' . getinfo('site_admin_url'). 'page/type/' . $nav['page_type_id'] .'"' . $selected . '>' . $nav['page_type_name'] . '</option>';
			
			if ($selected) $type_segment_name = $nav['page_type_name'];
			
			
			
		}
		
		echo '</select>';
	}
	
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
			. t('Статус')
			. ':</strong> ';
	
	$all_status = array('publish', 'draft', 'private');
	
	echo '<select class="admin_page_filtr">';
	
	$selected = (!mso_segment(4) and mso_segment(3) != 'status') ? '' : ' selected';
	
	echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любой') . '</option>';
	
	foreach($all_status as $status)
	{
		$selected = (mso_segment(4) == $status) ? ' selected' : '';
			
		echo '<option value="' . getinfo('site_admin_url'). 'page/status/' . $status .'"' . $selected . '>' . t($status) . '</option>';
		
			
	}
	
	echo '</select>';
	
	echo '</p>';	
	
	
	//  переход на указанный url
	echo '<script>
	$("select.admin_page_filtr").change(function(){
		window.location = $(this).val();
	});
	</script>';
		

	if (mso_segment(3) == 'category') 
	{
		if (mso_segment(4) != '') 
		{
			$par['cat_id'] = abs(intval(mso_segment(4)));
		}
	}
	elseif (mso_segment(3) == 'type') 
	{
		if (mso_segment(4) != '') 
		{
			$par['type'] = $type_segment_name;
		}
	}
	elseif (mso_segment(3) == 'status') 
	{
		if (in_array(mso_segment(4), $all_status)) 
		{
			$par['page_status'] = mso_segment(4);
		}
	}
	
	mso_remove_hook('content'); // удаляем все хуки по content
	
	$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	
	$all_pages = array(); // сразу список всех страниц для формы удаления
	
	$this_url = getinfo('site_admin_url') . 'page_edit/';
	$view_url = getinfo('siteurl') . 'page/';
	$view_url_cat = getinfo('siteurl') . 'category/';
	$view_url_tag = getinfo('siteurl') . 'tag/';
		
	if ($pages) // есть страницы
	{ 	
		foreach ($pages as $page) // выводим в цикле
		{
			// pr($page);
			// $act = '<a href="' . $this_url . $page['page_id'] . '">Изменить</a>';
			
			$page['page_title'] = htmlspecialchars($page['page_title']);
			
			
			
			if (!$page['page_title']) $page['page_title'] = 'no-title';
			
			$all_pages[$page['page_id']] = $page['page_id'] . ' - ' . $page['page_title']
				. ' - ' . $page['page_date_publish'] . ' - ' . $page['page_status'];
			
			$cats = '';
			$tags = '';
			
			foreach ($page['page_categories_detail'] as $key => $val)
			{
				$cats .= '<a href="' . $view_url_cat . $page['page_categories_detail'][$key]['category_slug'] . '">'
					. $page['page_categories_detail'][$key]['category_name'] . '</a>  ';
			}
			
			$cats = str_replace('  ', ', ', trim($cats));
			
			foreach ($page['page_tags'] as $val)
			{
				$tags .= '<a href="' . $view_url_tag . $val . '">' . $val . '</a>  ';
			}
			
			$tags = str_replace('  ', ', ', trim($tags));
			
			$title = '<a class="title" href="' . $this_url . $page['page_id'] . '">' . $page['page_title'] . '</a>'
					. ' [<a href="' . $view_url . $page['page_slug'] . '" target="_blank">' . t('Просмотр') . '</a>]';
			
		    $page_url =  $view_url . $page['page_slug'];
			
			
			
			$CI->table->add_row($page['page_id'], $title, social_count_get('googleplus_plusones' , $page_url ),
                    social_count_get('vkontakte_shares' , $page_url ),
                    social_count_get('twitter_retweets' , $page_url ),
                    social_count_get('facebook_likes' , $page_url )
                    );
		}
	

		$pagination['type'] = '';
		$pagination['range'] = 10;
		mso_hook('pagination', $pagination);
	
	
		echo mso_load_jquery('jquery.tablesorter.js') . '
			<script>
			$(function() {
				$("table.tablesorter").tablesorter();
			});
			</script>';
	

		echo $CI->table->generate(); // вывод подготовленной таблицы
	

		

		$pagination['type'] = '';
		$pagination['range'] = 10;
		//echo '<br>';
		mso_hook('pagination', $pagination);

		

	}
	else
	{
		echo '<h2>' . t('Страниц не найдено') . '</h2>';
		
	}
	
?>
